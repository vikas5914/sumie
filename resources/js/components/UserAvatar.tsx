import { Facehash } from 'facehash';
import { cn } from '../lib/utils';

interface UserAvatarProps {
    name: string;
    size?: number;
    className?: string;
}

const FACEHASH_COLORS = ['#f8e9a1', '#f6d6ad', '#d1e8ff', '#e6d6ff', '#d9f2e6', '#ffd9d9'];

export default function UserAvatar({ name, size = 38, className }: UserAvatarProps) {
    return (
        <div className={cn('inline-flex items-center justify-center border border-primary p-0.5', className)}>
            <Facehash name={name} size={size} colors={FACEHASH_COLORS} variant="solid" className="text-black" />
        </div>
    );
}
