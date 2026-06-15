import { onMounted } from 'vue'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

export function useReveal(selector) {
    onMounted(() => {
        gsap.utils.toArray(selector).forEach((item) => {
            gsap.from(item, {
                y: 60,
                opacity: 0,
                duration: 1,

                scrollTrigger: {
                    trigger: item,
                    start: 'top 85%'
                }
            })
        })
    })
}