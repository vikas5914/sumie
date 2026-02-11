import { Link } from '@inertiajs/react';

interface FilterItem {
    label: string;
    value: string;
}

interface FilterTabsProps {
    filters: FilterItem[];
    activeFilter: string;
    getHref: (value: string) => string;
    className?: string;
}

export default function FilterTabs({ filters, activeFilter, getHref, className = '' }: FilterTabsProps) {
    return (
        <div className={`grid auto-cols-fr grid-flow-col gap-2 pb-1 ${className}`}>
            {filters.map((f) => {
                const isActive = activeFilter === f.value;
                return (
                    <Link
                        key={f.value}
                        href={getHref(f.value)}
                        prefetch
                        only={['query', 'results', 'filter']}
                        preserveScroll
                        preserveState
                        replace
                        className={`inline-flex h-8 items-center justify-center border px-4 pt-px text-xs leading-[1] font-bold whitespace-nowrap uppercase transition-colors ${
                            isActive
                                ? 'border-primary bg-primary text-black shadow-[2px_2px_0_0_rgba(255,255,255,0.2)]'
                                : 'border-border-dark bg-transparent text-zinc-400 hover:border-primary hover:text-primary'
                        }`}
                    >
                        {f.label}
                    </Link>
                );
            })}
        </div>
    );
}
