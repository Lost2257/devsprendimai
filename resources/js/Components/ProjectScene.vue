<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { frameSmoothing, getScenePerformance } from '@/composables/useScenePerformance'

const props = defineProps({
    activeStep: { type: Number, default: 0 },
    accentColor: { type: String, default: '#22D3EE' },
})

const canvas = ref(null)
const sceneRoot = ref(null)
const sceneActive = ref(true)
let renderer
let scene
let camera
let world
let floatingPanels = []
let particles
let portalGroup
let portalRings = []
let keyLight
let mobileScene = false
let animationFrame
let resizeObserver
let visibilityObserver
let reducedMotion
let visible = true
let inViewport = true
let running = false
let targetFps = 45
let lastFrame = 0
let THREE
let gsap
const pointer = { x: 0, y: 0 }
const warp = { speed: 0 }

const renderScene = () => renderer?.render(scene, camera)
const handleVisibility = () => {
    visible = inViewport && !document.hidden
    sceneActive.value = visible
    if (visible && !reducedMotion) start()
}

const movePointer = (event) => {
    if (reducedMotion || window.matchMedia('(pointer: coarse)').matches) return
    pointer.x = (event.clientX / window.innerWidth - .5) * 2
    pointer.y = (event.clientY / window.innerHeight - .5) * 2
}

const resize = () => {
    if (!renderer || !camera || !sceneRoot.value) return
    const { width, height } = sceneRoot.value.getBoundingClientRect()
    renderer.setSize(width, height, false)
    camera.aspect = width / Math.max(height, 1)
    camera.updateProjectionMatrix()
    renderScene()
}

const animate = (time = 0) => {
    if (!visible || reducedMotion) {
        running = false
        animationFrame = 0
        return
    }
    animationFrame = requestAnimationFrame(animate)
    const interval = 1000 / targetFps
    const elapsed = time - lastFrame
    if (lastFrame && elapsed < interval) return
    const delta = lastFrame ? Math.min(elapsed, 50) : interval
    lastFrame = time - (elapsed % interval)
    const seconds = time * .001
    const cameraMoveEase = frameSmoothing(.045, delta)
    const cameraRotateEase = frameSmoothing(.04, delta)
    camera.position.x += (pointer.x * .65 - camera.position.x) * cameraMoveEase
    camera.position.y += (.55 - pointer.y * .34 - camera.position.y) * cameraMoveEase
    camera.rotation.y += (-pointer.x * .035 - camera.rotation.y) * cameraRotateEase
    camera.rotation.x += (pointer.y * .025 - camera.rotation.x) * cameraRotateEase

    floatingPanels.forEach((panel, index) => {
        panel.position.y += (panel.userData.baseY + Math.sin(seconds * .55 + index * 1.7) * .15 - panel.position.y) * .035
        panel.rotation.z = Math.sin(seconds * .32 + index) * .018
    })
    if (particles) {
        particles.rotation.y = seconds * .012
        const position = particles.geometry.attributes.position
        for (let index = 2; index < position.array.length; index += 3) {
            position.array[index] += (.012 + warp.speed) * (delta / (1000 / 60))
            if (position.array[index] > 5) position.array[index] = -32 - Math.random() * 8
        }
        position.needsUpdate = true
    }
    if (portalGroup) {
        portalGroup.rotation.z = seconds * .025
        portalRings.forEach((ring, index) => {
            ring.rotation.z = seconds * (index % 2 ? -.09 : .065) + index * .42
            ring.rotation.x = Math.sin(seconds * .34 + index) * .08
        })
    }

    renderScene()
}

const start = () => {
    if (running || reducedMotion || !visible) return
    running = true
    lastFrame = 0
    animationFrame = requestAnimationFrame(animate)
}

const changeStep = (step) => {
    if (!world || !camera) return
    const targetRotation = (step % 5 - 2) * .025
    const targetDepth = (step % 4) * .48

    if (reducedMotion) {
        world.rotation.y = targetRotation
        world.position.z = targetDepth
        renderScene()
        return
    }

    gsap.to(world.rotation, { y: targetRotation, x: (step % 3 - 1) * .012, duration: 1.15, ease: 'power3.out' })
    gsap.to(world.position, { z: targetDepth, duration: 1.15, ease: 'power3.out' })
    gsap.to(camera, { fov: 46 + (step % 3) * 1.2, duration: .9, ease: 'power2.out', onUpdate: () => camera.updateProjectionMatrix() })
    gsap.killTweensOf(warp)
    warp.speed = mobileScene ? .12 : .28
    gsap.to(warp, { speed: 0, duration: 1.2, ease: 'power4.out' })
    if (keyLight) {
        const baseIntensity = mobileScene ? 18 : 26
        keyLight.intensity = baseIntensity * 2.15
        gsap.to(keyLight, { intensity: baseIntensity, duration: .85, ease: 'power3.out' })
    }
    if (portalGroup) {
        gsap.fromTo(portalGroup.scale, { x: 1.16, y: 1.16, z: 1.16 }, { x: 1, y: 1, z: 1, duration: 1.05, ease: 'expo.out' })
    }
}

watch(() => props.activeStep, changeStep)

onMounted(async () => {
    const [threeModule, gsapModule] = await Promise.all([import('three'), import('gsap')])
    if (!canvas.value || !sceneRoot.value) return
    THREE = threeModule
    gsap = gsapModule.default
    reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches
    const performance = getScenePerformance(640)
    const mobile = performance.mobile
    mobileScene = mobile
    targetFps = performance.targetFps
    const accent = new THREE.Color(props.accentColor)

    renderer = new THREE.WebGLRenderer({ canvas: canvas.value, alpha: true, antialias: !mobile, powerPreference: 'high-performance' })
    renderer.setPixelRatio(performance.pixelRatio)
    renderer.setClearColor(0x02060e, 0)
    renderer.outputColorSpace = THREE.SRGBColorSpace
    renderer.toneMapping = THREE.ACESFilmicToneMapping
    renderer.toneMappingExposure = .92

    scene = new THREE.Scene()
    scene.fog = new THREE.FogExp2(0x02060e, mobile ? .058 : .047)
    camera = new THREE.PerspectiveCamera(reducedMotion ? 47 : 56, 1, .1, 80)
    camera.position.set(0, .55, reducedMotion ? (mobile ? 8.8 : 7.6) : (mobile ? 11.2 : 13.5))

    world = new THREE.Group()
    scene.add(world)

    scene.add(new THREE.AmbientLight(0x7c91ad, .35))
    keyLight = new THREE.PointLight(accent, reducedMotion ? (mobile ? 18 : 26) : 0, 22, 2)
    keyLight.position.set(1, 1.5, 3)
    scene.add(keyLight)
    const rimLight = new THREE.PointLight(accent, 15, 28, 2)
    rimLight.position.set(-7, 2, -8)
    scene.add(rimLight)

    const floor = new THREE.Mesh(
        new THREE.PlaneGeometry(52, 70),
        new THREE.MeshStandardMaterial({ color: 0x02060a, metalness: .88, roughness: .24, transparent: true, opacity: .86 }),
    )
    floor.rotation.x = -Math.PI / 2
    floor.position.set(0, -2.72, -18)
    world.add(floor)

    const grid = new THREE.GridHelper(50, mobile ? 32 : 48, accent, accent)
    grid.position.set(0, -2.68, -18)
    grid.material.transparent = true
    grid.material.opacity = mobile ? .13 : .2
    world.add(grid)

    const ceiling = grid.clone()
    ceiling.position.y = 5.1
    ceiling.material = grid.material.clone()
    ceiling.material.opacity = mobile ? .04 : .075
    world.add(ceiling)

    portalGroup = new THREE.Group()
    portalGroup.position.set(mobile ? 0 : 2.15, .55, -3.1)
    const ringCount = mobile ? 2 : 4
    portalRings = Array.from({ length: ringCount }, (_, index) => {
        const ring = new THREE.Mesh(
            new THREE.TorusGeometry(2.35 + index * .58, .018 + index * .005, 8, mobile ? 64 : 110),
            new THREE.MeshBasicMaterial({
                color: accent, transparent: true, opacity: .32 - index * .045,
                blending: THREE.AdditiveBlending, depthWrite: false,
            }),
        )
        ring.scale.y = .72 + index * .035
        portalGroup.add(ring)
        return ring
    })
    const portalCore = new THREE.Mesh(
        new THREE.CircleGeometry(mobile ? 2.7 : 3.8, mobile ? 48 : 80),
        new THREE.MeshBasicMaterial({
            color: accent, transparent: true, opacity: mobile ? .025 : .04,
            blending: THREE.AdditiveBlending, depthWrite: false,
        }),
    )
    portalCore.scale.y = .73
    portalCore.position.z = -.08
    portalGroup.add(portalCore)
    world.add(portalGroup)

    for (let index = 0; index < 5; index += 1) {
        const scale = 1 - index * .095
        const frameGeometry = new THREE.EdgesGeometry(new THREE.BoxGeometry(11 * scale, 6.5 * scale, .12))
        const frameMaterial = new THREE.LineBasicMaterial({ color: accent, transparent: true, opacity: .31 - index * .045 })
        const frame = new THREE.LineSegments(frameGeometry, frameMaterial)
        frame.position.set(0, .8, -3.5 - index * 4.7)
        world.add(frame)
    }

    const panelMaterial = new THREE.MeshStandardMaterial({
        color: 0x07111d, emissive: accent, emissiveIntensity: .075, metalness: .55, roughness: .18,
        transparent: true, opacity: .64,
    })
    const panelPositions = mobile
        ? [[-4.6, .7, -7.5], [4.5, -.2, -10.5]]
        : [[-5.5, 1.3, -5.5], [5.4, -.2, -8.5], [-4.8, -1.2, -13.5], [4.7, 2, -16]]

    floatingPanels = panelPositions.map(([x, y, z], index) => {
        const panel = new THREE.Mesh(new THREE.BoxGeometry(3.15, 1.9, .14), panelMaterial.clone())
        panel.position.set(x, y, z)
        panel.rotation.y = x < 0 ? .33 : -.33
        panel.userData.baseY = y
        world.add(panel)
        return panel
    })

    const particleCount = mobile ? 70 : 180
    const positions = new Float32Array(particleCount * 3)
    for (let index = 0; index < particleCount; index += 1) {
        positions[index * 3] = (Math.random() - .5) * 24
        positions[index * 3 + 1] = (Math.random() - .5) * 11
        positions[index * 3 + 2] = -Math.random() * 32 + 5
    }
    const particleGeometry = new THREE.BufferGeometry()
    particleGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3))
    particles = new THREE.Points(particleGeometry, new THREE.PointsMaterial({ color: accent, size: mobile ? .025 : .035, transparent: true, opacity: .65, sizeAttenuation: true }))
    world.add(particles)

    resizeObserver = new ResizeObserver(resize)
    resizeObserver.observe(sceneRoot.value)
    visibilityObserver = new IntersectionObserver(([entry]) => {
        inViewport = entry.isIntersecting
        handleVisibility()
    }, { threshold: .02 })
    visibilityObserver.observe(sceneRoot.value)
    window.addEventListener('pointermove', movePointer, { passive: true })
    document.addEventListener('visibilitychange', handleVisibility)
    changeStep(props.activeStep)
    resize()
    if (reducedMotion) {
        renderScene()
    } else {
        const targetZ = mobile ? 8.8 : 7.6
        gsap.to(camera.position, { z: targetZ, duration: 1.75, ease: 'power4.out' })
        gsap.to(camera, { fov: 47, duration: 1.55, ease: 'power3.out', onUpdate: () => camera.updateProjectionMatrix() })
        gsap.to(keyLight, { intensity: mobile ? 18 : 26, duration: 1.35, ease: 'power3.out' })
        gsap.fromTo(world.scale, { x: .86, y: .86, z: .86 }, { x: 1, y: 1, z: 1, duration: 1.8, ease: 'expo.out' })
        start()
    }
})

onUnmounted(() => {
    cancelAnimationFrame(animationFrame)
    gsap?.killTweensOf(world?.rotation)
    gsap?.killTweensOf(world?.position)
    gsap?.killTweensOf(camera)
    gsap?.killTweensOf(camera?.position)
    gsap?.killTweensOf(keyLight)
    gsap?.killTweensOf(portalGroup?.scale)
    gsap?.killTweensOf(warp)
    resizeObserver?.disconnect()
    visibilityObserver?.disconnect()
    window.removeEventListener('pointermove', movePointer)
    document.removeEventListener('visibilitychange', handleVisibility)
    scene?.traverse((object) => {
        object.geometry?.dispose?.()
        if (Array.isArray(object.material)) object.material.forEach((material) => material.dispose())
        else object.material?.dispose?.()
    })
    renderer?.dispose()
})
</script>

<template>
    <div ref="sceneRoot" class="webgl-scene" :class="{ 'is-active': sceneActive }" aria-hidden="true">
        <canvas ref="canvas"></canvas>
        <div class="webgl-vignette"></div>
    </div>
</template>

<style scoped>
.webgl-scene{position:absolute;z-index:-3;inset:0;overflow:hidden;background:#02060e;pointer-events:none}.webgl-scene canvas{display:block;width:100%;height:100%}.webgl-vignette{position:absolute;inset:0;background:radial-gradient(circle at 58% 48%,transparent 28%,rgba(1,4,9,.28) 62%,rgba(1,3,7,.82) 100%),linear-gradient(180deg,rgba(1,4,9,.18),transparent 32%,rgba(1,4,9,.26));pointer-events:none}
</style>
