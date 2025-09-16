import { ref, computed } from 'vue'
import ListLocales from '~/useCases/CoreConfiguration/Locale/ListLocales'
import type { LocaleJsonld } from '~/services/client'

const currentLocale = ref('fr_FR') // Default locale

export function UseLocale() {
  const { data: locales } = ListLocales()

  const setLocale = (locale: string) => {
    currentLocale.value = locale
  }

  const getLocale = computed(() => currentLocale.value)

  const getAvailableLocales = computed((): LocaleJsonld[] => {
    return locales.value?.member ?? []
  })

  return {
    currentLocale,
    setLocale,
    getLocale,
    getAvailableLocales,
  }
}
