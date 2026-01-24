import { Link } from '@inertiajs/react';

interface MangaCardProps {
    title: string;
    subtitle?: string;
    imageUrl: string;
    badge?: {
        text: string;
        color?: string; // 'primary' | 'red' | etc. defaults to primary
        position?: 'top-left' | 'top-right' | 'bottom-right';
    };
    rating?: number;
    href?: string;
    className?: string;
}

export default function MangaCard({ title, subtitle, imageUrl, badge, rating, href = '#', className = '' }: MangaCardProps) {
    return (
        <Link href={href} className={`group flex cursor-pointer flex-col gap-2 ${className}`}>
            <div className="relative aspect-[2/3] w-full overflow-hidden border border-border-dark bg-zinc-800">
                <div
                    className="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-500 group-hover:scale-105"
                    style={{ backgroundImage: `url("${imageUrl}")` }}
                ></div>

                {/* Badge */}
                {badge && (
                    <div
                        className={`absolute border px-2 py-0.5 text-[10px] font-bold uppercase ${
                            badge.position === 'top-right' ? 'top-2 right-2' : badge.position === 'top-left' ? 'top-2 left-2' : 'right-0 bottom-0'
                        } ${badge.color === 'red' ? 'border-red-500 bg-red-500 text-white' : 'border-primary bg-primary/90 text-background-dark'}`}
                    >
                        {badge.text}
                    </div>
                )}

                {/* Rating */}
                {rating && (
                    <div className="absolute top-2 right-2 flex items-center gap-1 border border-border-dark bg-background-dark/80 px-2 py-0.5 text-[10px] font-bold text-text-light">
                        <span className="material-symbols-outlined text-[12px] text-primary">star</span> {rating}
                    </div>
                )}
            </div>
            <div>
                <h4 className="truncate text-sm font-bold text-text-light uppercase group-hover:text-primary">{title}</h4>
                {subtitle && <p className="text-xs text-zinc-500 uppercase">{subtitle}</p>}
            </div>
        </Link>
    );
}
