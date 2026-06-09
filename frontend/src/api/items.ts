import type { Item, SortDirection, SortKey, StatusFilter } from '../types/item'

// Calls go through the Vite dev-server proxy (/api -> backend), so only the
// frontend port is exposed outside Docker. Override with VITE_API_URL if needed.
const API_URL = import.meta.env.VITE_API_URL ?? '/api'

export interface FetchItemsParams {
  status: StatusFilter
  search: string
  sort: SortKey
  dir: SortDirection
}

interface ItemsResponse {
  data?: Item[]
  error?: string
}

export async function fetchItems(
  params: FetchItemsParams,
  signal?: AbortSignal,
): Promise<Item[]> {
  const query = new URLSearchParams({
    status: params.status,
    search: params.search,
    sort: params.sort,
    dir: params.dir,
  })

  const response = await fetch(`${API_URL}/?${query.toString()}`, { signal })

  const payload = (await response.json().catch(() => null)) as ItemsResponse | null

  if (!response.ok || payload?.error) {
    throw new Error(payload?.error ?? `Request failed with status ${response.status}`)
  }

  return payload?.data ?? []
}
