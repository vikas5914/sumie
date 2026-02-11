import type { ReactNode } from 'react';

interface HeaderProps {
    className?: string;
    children: ReactNode;
}

export default function Header({ className = '', children }: HeaderProps) {
    return (
        <header
            className={`sticky top-0 z-20 flex flex-col border-b border-border-dark bg-background-dark/95 p-4 pt-0 backdrop-blur-sm ${className}`}
        >
            {children}
        </header>
    );
}
