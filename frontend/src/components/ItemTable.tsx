import type { Item, SortDirection, SortKey } from '../types/item'
import { SortableHeader } from './SortableHeader'

interface ItemTableProps {
  items: Item[]
  sort: SortKey
  direction: SortDirection
  loading: boolean
  onSort: (key: SortKey) => void
}

export function ItemTable({ items, sort, direction, loading, onSort }: ItemTableProps) {
  if (!loading && items.length === 0) {
    return <p className="state state--empty">No items match your filters.</p>
  }

  return (
    <div className="table-wrapper" aria-busy={loading}>
      <table className="item-table">
        <thead>
          <tr>
            <SortableHeader
              label="Name"
              sortKey="name"
              activeSort={sort}
              direction={direction}
              onSort={onSort}
            />
            <SortableHeader
              label="Status"
              sortKey="active"
              activeSort={sort}
              direction={direction}
              onSort={onSort}
            />
          </tr>
        </thead>
        <tbody>
          {items.map((item) => (
            <tr key={item.name}>
              <td data-label="Name">{item.name}</td>
              <td data-label="Status">
                <span
                  className={`badge ${item.active ? 'badge--active' : 'badge--inactive'}`}
                >
                  {item.active ? 'Active' : 'Inactive'}
                </span>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}
