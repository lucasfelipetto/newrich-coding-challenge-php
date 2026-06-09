import { FilterBar } from './components/FilterBar'
import { ItemTable } from './components/ItemTable'
import { useItems } from './hooks/useItems'

export default function App() {
  const {
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
  } = useItems()

  return (
    <main className="app">
      <header className="app__header">
        <h1 className="app__title">Items Directory</h1>
        <p className="app__subtitle">Filter, search and sort the items.</p>
      </header>

      <FilterBar
        status={status}
        search={search}
        onStatusChange={setStatus}
        onSearchChange={setSearch}
      />

      {error && (
        <p className="state state--error" role="alert">
          Failed to load items: {error}
        </p>
      )}

      <ItemTable
        items={items}
        sort={sort}
        direction={dir}
        loading={loading}
        onSort={toggleSort}
      />

      {loading && <p className="state state--loading">Loading…</p>}
    </main>
  )
}
