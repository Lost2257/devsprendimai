<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { frameSmoothing, getScenePerformance } from '@/composables/useScenePerformance'

const props = defineProps({
    accentColor: { type: String, default: '#22D3EE' },
    variant: { type: String, default: 'default' },
})

const root = ref(null)
const canvas = ref(null)
const sceneActive = ref(true)
let THREE
let renderer
let scene
let camera
let world
let particles
let panels = []
let resizeObserver
let visibilityObserver
let animationFrame = 0
let running = false
let visible = true
let reducedMotion = false
let mobile = false
let inViewport = true
let targetFps = 45
let lastFrame = 0
let scrollDepth = 0
const pointer = { x: 0, y: 0 }

const render = () => renderer?.render(scene, camera)

const resize = () => {
    if (!renderer || !camera || !root.value) return
    const { width, height } = root.value.getBoundingClientRect()
    renderer.setSize(width, height, false)
    camera.aspect = width / Math.max(height, 1)
    camera.updateProjectionMatrix()
    render()
}

const movePointer = (event) => {
    if (reducedMotion || mobile || !root.value) return
    const rect = root.value.getBoundingClientRect()
    pointer.x = Math.min(1, Math.max(-1, ((event.clientX - rect.left) / rect.width - .5) * 2))
    pointer.y = Math.min(1, Math.max(-1, ((event.clientY - rect.top) / rect.height - .5) * 2))
}

const resetPointer = () => {
    pointer.x = 0
    pointer.y = 0
}

const updateScroll = () => {
    if (!root.value) return
    const rect = root.value.getBoundingClientRect()
    scrollDepth = Math.min(1, Math.max(0, -rect.top / Math.max(rect.height, 1)))
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
    const cameraEase = frameSmoothing(.035, delta)
    const worldEase = frameSmoothing(.028, delta)
    camera.position.x += (pointer.x * .55 - camera.position.x) * cameraEase
    camera.position.y += (1 - pointer.y * .3 + scrollDepth * .55 - camera.position.y) * cameraEase
    camera.rotation.y += (-pointer.x * .028 - camera.rotation.y) * cameraEase
    world.rotation.x += ((scrollDepth * .045) - world.rotation.x) * worldEase

    panels.forEach((panel, index) => {
        panel.position.y = panel.userData.y + Math.sin(seconds * .48 + index * 1.8) * .13
        panel.rotation.z = Math.sin(seconds * .25 + index) * .018
    })

    if (particles) {
        const positions = particles.geometry.attributes.position
        for (let index = 2; index < positions.array.length; index += 3) {
            positions.array[index] += .009 * (delta / (1000 / 60))
            if (positions.array[index] > 6) positions.array[index] = -30 - Math.random() * 8
        }
        positions.needsUpdate = true
        particles.rotation.y = seconds * .009
    }

    render()
}

const start = () => {
    if (running || reducedMotion || !visible) return
    running = true
    lastFrame = 0
    animationFrame = requestAnimationFrame(animate)
}

const syncVisibility = () => {
    visible = inViewport && !document.hidden
    sceneActive.value = visible
    if (visible) start()
}

onMounted(async () => {
    THREE = await import('three')
    if (!root.value || !canvas.value) return

    reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches
    const performance = getScenePerformance(767)
    mobile = performance.mobile
    targetFps = performance.targetFps
    const accent = new THREE.Color(props.accentColor)

    try {
        renderer = new THREE.WebGLRenderer({
            canvas: canvas.value,
            alpha: true,
            antialias: !mobile,
            powerPreference: 'high-performance',
        })
    } catch {
        root.value.classList.add('webgl-unavailable')
        return
    }

    renderer.setPixelRatio(performance.pixelRatio)
    renderer.setClearColor(0x02060e, 0)
    renderer.outputColorSpace = THREE.SRGBColorSpace
    renderer.toneMapping = THREE.ACESFilmicToneMapping
    renderer.toneMappingExposure = 1.08

    scene = new THREE.Scene()
    scene.fog = new THREE.FogExp2(0x02060e, mobile ? .052 : .036)
    camera = new THREE.PerspectiveCamera(50, 1, .1, 70)
    camera.position.set(0, 1, mobile ? 8.5 : 7.15)

    world = new THREE.Group()
    scene.add(world)
    scene.add(new THREE.AmbientLight(0x7c91ad, .42))

    const keyLight = new THREE.PointLight(accent, mobile ? 19 : 34, 27, 2)
    keyLight.position.set(mobile ? 1.2 : 4.5, 2.2, 3)
    scene.add(keyLight)

    const floor = new THREE.Mesh(
        new THREE.PlaneGeometry(44, 60),
        new THREE.MeshStandardMaterial({ color: 0x02060a, metalness: .8, roughness: .28, transparent: true, opacity: .7 }),
    )
    floor.rotation.x = -Math.PI / 2
    floor.position.set(0, -2.7, -17)
    world.add(floor)

    const grid = new THREE.GridHelper(46, mobile ? 26 : 42, accent, accent)
    grid.position.set(0, -2.66, -16)
    grid.material.transparent = true
    grid.material.opacity = mobile ? .17 : .29
    world.add(grid)

    const ceiling = grid.clone()
    ceiling.position.y = 5.25
    ceiling.material = grid.material.clone()
    ceiling.material.opacity = mobile ? .065 : .11
    world.add(ceiling)

    const leftWall = grid.clone()
    leftWall.rotation.z = Math.PI / 2
    leftWall.position.set(-8.2, .55, -16)
    leftWall.material = grid.material.clone()
    leftWall.material.opacity = mobile ? .055 : .13
    world.add(leftWall)

    const rightWall = leftWall.clone()
    rightWall.position.x = 8.2
    rightWall.material = leftWall.material.clone()
    world.add(rightWall)

    const depthGeometry = new THREE.BufferGeometry()
    depthGeometry.setAttribute('position', new THREE.BufferAttribute(new Float32Array([
        -9, -2.64, 4, -2.6, -2.64, -34,
        9, -2.64, 4, 2.6, -2.64, -34,
        -9, 5.1, 4, -2.6, 5.1, -34,
        9, 5.1, 4, 2.6, 5.1, -34,
        -8.1, -2.5, -2, -8.1, 4.9, -28,
        8.1, -2.5, -2, 8.1, 4.9, -28,
    ]), 3))
    const depthLines = new THREE.LineSegments(
        depthGeometry,
        new THREE.LineBasicMaterial({ color: accent, transparent: true, opacity: mobile ? .13 : .28, blending: THREE.AdditiveBlending }),
    )
    world.add(depthLines)

    for (let index = 0; index < (mobile ? 3 : 5); index += 1) {
        const scale = 1 - index * .09
        const geometry = new THREE.EdgesGeometry(new THREE.BoxGeometry(12 * scale, 7 * scale, .12))
        const material = new THREE.LineBasicMaterial({ color: accent, transparent: true, opacity: .36 - index * .048 })
        const frame = new THREE.LineSegments(geometry, material)
        frame.position.set(0, .8, -5 - index * 5)
        world.add(frame)
    }

    const panelMaterial = new THREE.MeshStandardMaterial({
        color: 0x07111d,
        emissive: accent,
        emissiveIntensity: .1,
        metalness: .55,
        roughness: .2,
        transparent: true,
        opacity: .64,
    })
    const panelPositions = mobile
        ? [[-3.8, 1.4, -8], [4.1, -1.1, -12]]
        : [[-5.6, 1.8, -6], [6.1, -1, -9], [-4.8, -1.5, -14], [5.2, 2.1, -17]]
    panels = panelPositions.map(([x, y, z]) => {
        const panel = new THREE.Mesh(new THREE.BoxGeometry(3.1, 1.75, .1), panelMaterial.clone())
        panel.position.set(x, y, z)
        panel.rotation.y = x < 0 ? .34 : -.34
        panel.userData.y = y
        world.add(panel)
        return panel
    })

    const particleCount = mobile ? 55 : 150
    const positions = new Float32Array(particleCount * 3)
    for (let index = 0; index < particleCount; index += 1) {
        positions[index * 3] = (Math.random() - .5) * 25
        positions[index * 3 + 1] = (Math.random() - .5) * 11
        positions[index * 3 + 2] = -Math.random() * 36 + 5
    }
    const particleGeometry = new THREE.BufferGeometry()
    particleGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3))
    particles = new THREE.Points(
        particleGeometry,
        new THREE.PointsMaterial({ color: accent, size: mobile ? .03 : .043, transparent: true, opacity: .78, sizeAttenuation: true }),
    )
    world.add(particles)

    resizeObserver = new ResizeObserver(resize)
    resizeObserver.observe(root.value)
    visibilityObserver = new IntersectionObserver(([entry]) => {
        inViewport = entry.isIntersecting
        syncVisibility()
    }, { threshold: .02 })
    visibilityObserver.observe(root.value)
    window.addEventListener('pointermove', movePointer, { passive: true })
    document.documentElement.addEventListener('mouseleave', resetPointer)
    window.addEventListener('scroll', updateScroll, { passive: true })
    document.addEventListener('visibilitychange', syncVisibility)

    resize()
    updateScroll()
    reducedMotion ? render() : start()
})

onUnmounted(() => {
    visible = false
    running = false
    cancelAnimationFrame(animationFrame)
    resizeObserver?.disconnect()
    visibilityObserver?.disconnect()
    window.removeEventListener('pointermove', movePointer)
    document.documentElement.removeEventListener('mouseleave', resetPointer)
    window.removeEventListener('scroll', updateScroll)
    document.removeEventListener('visibilitychange', syncVisibility)
    scene?.traverse((object) => {
        object.geometry?.dispose?.()
        if (Array.isArray(object.material)) object.material.forEach((material) => material.dispose())
        else object.material?.dispose?.()
    })
    renderer?.dispose()
})
</script>

<template>
    <div ref="root" class="hero-scene" :class="{ 'is-active': sceneActive }" aria-hidden="true">
        <canvas ref="canvas"></canvas>
        <div class="hero-css-room">
            <div class="hero-grid hero-grid-floor"></div>
            <div class="hero-grid hero-grid-ceiling"></div>
            <div class="hero-wall hero-wall-left"></div>
            <div class="hero-wall hero-wall-right"></div>
            <div class="hero-gate hero-gate-one"></div>
            <div class="hero-gate hero-gate-two"></div>
            <div class="hero-gate hero-gate-three"></div>
            <div class="hero-beam hero-beam-left"></div>
            <div class="hero-beam hero-beam-right"></div>
        </div>
        <div class="hero-scene-vignette"></div>
    </div>
</template>

<style scoped>
.hero-scene{position:absolute;z-index:0;inset:0;overflow:hidden;pointer-events:none;background:#02060e;opacity:1;mask-image:linear-gradient(black 0 91%,transparent 100%);-webkit-mask-image:linear-gradient(black 0 91%,transparent 100%)}.hero-scene::before{content:'';position:absolute;z-index:1;inset:0;background:radial-gradient(circle at 72% 46%,color-mix(in srgb,v-bind(accentColor) 16%,transparent),transparent 34%),radial-gradient(circle at 18% 75%,color-mix(in srgb,v-bind(accentColor) 6%,transparent),transparent 24%);pointer-events:none}.hero-scene canvas{position:relative;z-index:1;display:block;width:100%;height:100%}.hero-css-room{position:absolute;z-index:2;inset:-4%;overflow:hidden;perspective:850px;transform-style:preserve-3d;opacity:.82}.hero-grid,.hero-wall{position:absolute;background-image:linear-gradient(color-mix(in srgb,v-bind(accentColor) 18%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,v-bind(accentColor) 18%,transparent) 1px,transparent 1px);background-size:58px 58px}.hero-grid{left:-20%;width:140%;height:75%;mask-image:linear-gradient(90deg,transparent,black 18%,black 82%,transparent);-webkit-mask-image:linear-gradient(90deg,transparent,black 18%,black 82%,transparent);animation:hero-grid-travel 13s linear infinite}.hero-grid-floor{bottom:-29%;transform-origin:50% 0;transform:perspective(650px) rotateX(62deg)}.hero-grid-ceiling{top:-31%;opacity:.48;transform-origin:50% 100%;transform:perspective(650px) rotateX(-62deg);animation-direction:reverse}.hero-wall{top:-8%;bottom:-8%;width:56%;opacity:.32;mask-image:linear-gradient(transparent,black 22%,black 78%,transparent);-webkit-mask-image:linear-gradient(transparent,black 22%,black 78%,transparent)}.hero-wall-left{left:-40%;transform-origin:100% 50%;transform:perspective(720px) rotateY(68deg)}.hero-wall-right{right:-40%;transform-origin:0 50%;transform:perspective(720px) rotateY(-68deg)}.hero-gate{position:absolute;left:50%;top:49%;width:84%;height:74%;border:1px solid color-mix(in srgb,v-bind(accentColor) 30%,transparent);border-radius:1.6rem;box-shadow:inset 0 0 70px rgba(0,0,0,.5),0 0 28px color-mix(in srgb,v-bind(accentColor) 7%,transparent)}.hero-gate-one{transform:translate(-50%,-50%) translateZ(-80px)}.hero-gate-two{opacity:.6;transform:translate(-50%,-50%) translateZ(-330px) scale(.82)}.hero-gate-three{opacity:.3;transform:translate(-50%,-50%) translateZ(-600px) scale(.64)}.hero-beam{position:absolute;top:-12%;bottom:-12%;width:1px;background:linear-gradient(transparent,v-bind(accentColor),transparent);box-shadow:0 0 22px v-bind(accentColor);opacity:.38}.hero-beam-left{left:8%;transform:rotate(7deg)}.hero-beam-right{right:8%;transform:rotate(-7deg)}.hero-scene-vignette{position:absolute;z-index:3;inset:0;background:radial-gradient(circle at 68% 45%,transparent 24%,rgba(2,6,14,.08) 58%,rgba(2,6,14,.45) 100%),linear-gradient(90deg,rgba(2,6,14,.48),transparent 43%,rgba(2,6,14,.08));pointer-events:none}.webgl-unavailable canvas{display:none}
@keyframes hero-grid-travel{to{background-position:0 58px,58px 0}}
.hero-scene:not(.is-active) .hero-grid{animation-play-state:paused}
@media(max-width:767px){.hero-scene{inset:0;mask-image:linear-gradient(black 0 94%,transparent 100%);-webkit-mask-image:linear-gradient(black 0 94%,transparent 100%)}.hero-css-room{inset:-7%;opacity:.62}.hero-grid,.hero-wall{background-size:40px 40px}.hero-grid{height:68%}.hero-grid-floor{bottom:-19%}.hero-grid-ceiling{top:-24%;opacity:.33}.hero-wall{opacity:.2}.hero-gate{width:96%;height:70%;border-radius:1rem}.hero-beam{opacity:.22}.hero-scene-vignette{background:radial-gradient(circle at 66% 53%,transparent 19%,rgba(2,6,14,.08) 58%,rgba(2,6,14,.42) 100%),linear-gradient(90deg,rgba(2,6,14,.38),transparent 72%)}}
@media(prefers-reduced-motion:reduce){.hero-grid{animation:none}.hero-css-room{opacity:.52}}
</style>
