interface SearchInputProps {
    className?: string;
    placeholder?: string;
}

export default function SearchInput({
    className = '',
    placeholder = 'SEARCH MANGA, AUTHORS, OR GENRES...',
}: SearchInputProps) {
    return (
        <div className={`relative w-full ${className}`}>
            <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <span className="material-symbols-outlined text-xl text-zinc-500">search</span>
            </div>
            <input
                className="block w-full border border-border-dark bg-surface-dark py-3 pr-4 pl-11 text-sm text-text-light placeholder-zinc-500 shadow-none outline-none focus:ring-2 focus:ring-primary"
                placeholder={placeholder}
                type="text"
            />
        </div>
    );
}
