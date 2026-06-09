import type { StatusFilter } from '../types/item'

interface FilterBarProps {
  status: StatusFilter
  search: string
  onStatusChange: (status: StatusFilter) => void
  onSearchChange: (search: string) => void
}

const STATUS_OPTIONS: { value: StatusFilter; label: string }[] = [
  { value: 'all', label: 'All' },
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
]

export function FilterBar({
  status,
  search,
  onStatusChange,
  onSearchChange,
}: FilterBarProps) {
  return (
    <div className="filter-bar">
      <input
        type="search"
        className="filter-bar__search"
        placeholder="Search by name…"
        value={search}
        onChange={(event) => onSearchChange(event.target.value)}
        aria-label="Search by name"
      />
      <div className="filter-bar__status" role="group" aria-label="Filter by status">
        {STATUS_OPTIONS.map((option) => (
          <button
            key={option.value}
            type="button"
            className={`chip${status === option.value ? ' chip--active' : ''}`}
            onClick={() => onStatusChange(option.value)}
            aria-pressed={status === option.value}
          >
            {option.label}
          </button>
        ))}
      </div>
    </div>
  )
}
