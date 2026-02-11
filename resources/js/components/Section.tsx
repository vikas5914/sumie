import type { ReactNode } from 'react';

interface SectionProps {
    title?: string;
    action?: ReactNode;
    children: ReactNode;
    className?: string;
}

export default function Section({ title, action, children, className = '' }: SectionProps) {
    return (
        <section className={`flex flex-col gap-3 ${className}`}>
            {(title || action) && (
                <div className="flex items-center justify-between px-4">
                    {title && <h3 className="text-lg font-bold text-text-light uppercase">{title}</h3>}
                    {action}
                </div>
            )}
            {children}
        </section>
    );
}
