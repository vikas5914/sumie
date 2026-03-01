import type { LucideIcon, LucideProps } from 'lucide-react';
import {
    ArrowLeft,
    ArrowRight,
    ArrowUpDown,
    Bell,
    Bookmark,
    BookMarked,
    BookmarkPlus,
    BookOpen,
    Check,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    ChevronUp,
    CircleHelp,
    EllipsisVertical,
    ExternalLink,
    House,
    LayoutGrid,
    ListFilter,
    LoaderCircle,
    Pencil,
    Play,
    Power,
    RefreshCcw,
    RefreshCw,
    Search,
    Settings2,
    Share2,
    SlidersHorizontal,
    Star,
    Terminal,
    User,
    X,
} from 'lucide-react';
import { cn } from '@/lib/utils';

const iconMap: Record<string, LucideIcon> = {
    arrow_back: ArrowLeft,
    arrow_drop_down: ChevronDown,
    arrow_drop_up: ChevronUp,
    arrow_forward: ArrowRight,
    bookmark: Bookmark,
    bookmark_add: BookmarkPlus,
    bookmarks: BookMarked,
    check: Check,
    chevron_left: ChevronLeft,
    chevron_right: ChevronRight,
    close: X,
    edit: Pencil,
    filter_list: ListFilter,
    grid_view: LayoutGrid,
    home: House,
    keyboard_arrow_down: ChevronDown,
    manage_search: Search,
    menu_book: BookOpen,
    more_vert: EllipsisVertical,
    notifications: Bell,
    open_in_new: ExternalLink,
    person: User,
    play_arrow: Play,
    power_settings_new: Power,
    progress_activity: LoaderCircle,
    refresh: RefreshCcw,
    search: Search,
    settings_suggest: Settings2,
    share: Share2,
    sort: ArrowUpDown,
    star: Star,
    sync: RefreshCw,
    terminal: Terminal,
    tune: SlidersHorizontal,
};

interface AppIconProps extends Omit<LucideProps, 'name'> {
    name: string;
}

export default function AppIcon({ name, className, ...props }: AppIconProps) {
    const IconComponent = iconMap[name] ?? CircleHelp;

    return <IconComponent aria-hidden="true" className={cn('inline-block shrink-0 align-middle', className)} size="1em" {...props} />;
}
