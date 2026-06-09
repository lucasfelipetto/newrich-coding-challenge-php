export interface Item {
  name: string
  active: boolean
}

export type StatusFilter = 'all' | 'active' | 'inactive'
export type SortKey = 'name' | 'active'
export type SortDirection = 'asc' | 'desc'
