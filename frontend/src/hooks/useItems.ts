import { useEffect, useState } from 'react'
import { fetchItems } from '../api/items'
import type { Item, SortDirection, SortKey, StatusFilter } from '../types/item'

const SEARCH_DEBOUNCE_MS = 250

export function useItems() {
  const [items, setItems] = useState<Item[]>([])
  const [status, setStatus] = useState<StatusFilter>('all')
  const [search, setSearch] = useState('')
  const [sort, setSort] = useState<SortKey>('name')
  const [dir, setDir] = useState<SortDirection>('asc')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const [debouncedSearch, setDebouncedSearch] = useState(search)
  useEffect(() => {
    const timer = setTimeout(() => setDebouncedSearch(search), SEARCH_DEBOUNCE_MS)
    return () => clearTimeout(timer)
  }, [search])

  useEffect(() => {
    const controller = new AbortController()
    let active = true

    setLoading(true)
    setError(null)

    fetchItems({ status, search: debouncedSearch, sort, dir }, controller.signal)
      .then((data) => {
        if (active) setItems(data)
      })
      .catch((err: unknown) => {
        if (!active || (err instanceof DOMException && err.name === 'AbortError')) return
        setError(err instanceof Error ? err.message : 'Unknown error')
      })
      .finally(() => {
        if (active) setLoading(false)
      })

    return () => {
      active = false
      controller.abort()
    }
  }, [status, debouncedSearch, sort, dir])

  function toggleSort(key: SortKey) {
    if (sort === key) {
      setDir((prev) => (prev === 'asc' ? 'desc' : 'asc'))
      return
    }
    setSort(key)
    setDir('asc')
  }

  return {
    items,
    loading,
    error,
    status,
    setStatus,
    search,
    setSearch,
    sort,
    dir,
    toggleSort,
  }
}
