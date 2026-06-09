import type { SortDirection, SortKey } from '../types/item'

interface SortableHeaderProps {
  label: string
  sortKey: SortKey
  activeSort: SortKey
  direction: SortDirection
  onSort: (key: SortKey) => void
}

export function SortableHeader({
  label,
  sortKey,
  activeSort,
  direction,
  onSort,
}: SortableHeaderProps) {
  const isActive = activeSort === sortKey
  const indicator = isActive ? (direction === 'asc' ? '▲' : '▼') : '↕'
  const ariaSort = isActive ? (direction === 'asc' ? 'ascending' : 'descending') : 'none'

  return (
    <th aria-sort={ariaSort} scope="col">
      <button
        type="button"
        className={`sort-header${isActive ? ' sort-header--active' : ''}`}
        onClick={() => onSort(sortKey)}
      >
        <span>{label}</span>
        <span className="sort-header__icon" aria-hidden="true">
          {indicator}
        </span>
      </button>
    </th>
  )
}
