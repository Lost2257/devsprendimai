export function getScenePerformance(mobileBreakpoint = 767) {
    const mobile = window.matchMedia(`(max-width: ${mobileBreakpoint}px)`).matches
    const coarsePointer = window.matchMedia('(pointer: coarse)').matches
    const memory = Number(navigator.deviceMemory || 0)
    const cores = Number(navigator.hardwareConcurrency || 0)
    const lowPower = (memory > 0 && memory <= 4) || (cores > 0 && cores <= 4)

    return {
        mobile,
        coarsePointer,
        lowPower,
        pixelRatio: Math.min(window.devicePixelRatio || 1, mobile || lowPower ? 1 : 1.3),
        targetFps: mobile || coarsePointer || lowPower ? 30 : 45,
    }
}

export function frameSmoothing(base, deltaMs) {
    return 1 - Math.pow(1 - base, Math.min(deltaMs, 50) / (1000 / 60))
}
