import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useLocale() {
    const page = usePage()
    const locale = computed(() => page.props.locale || 'lt')
    const isEnglish = computed(() => locale.value === 'en')
    const prefix = computed(() => isEnglish.value ? '/en' : '')

    const localize = (path = '/') => {
        if (/^(https?:|mailto:|tel:|#)/.test(path)) return path
        const clean = path.replace(/^\/en(?=\/|$)/, '') || '/'
        return `${prefix.value}${clean === '/' ? '' : clean}` || '/'
    }

    const languageUrl = computed(() => {
        const current = (page.url || '/').split('?')[0].split('#')[0]
        if (isEnglish.value) return current.replace(/^\/en(?=\/|$)/, '') || '/'
        return `/en${current === '/' ? '' : current}`
    })

    return { locale, isEnglish, prefix, localize, languageUrl }
}
