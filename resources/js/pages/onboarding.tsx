import { Form, Head } from '@inertiajs/react';
import AppIcon from '../components/AppIcon';
import { store as onboardingStore } from '../routes/onboarding';

export default function Onboarding() {
    return (
        <>
            <Head title="Welcome to Sumie" />
            <div className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-background-dark font-mono text-text-light antialiased selection:bg-primary selection:text-black">
                {/* Subtle Gradient Background */}
                <div className="pointer-events-none absolute inset-0 bg-gradient-to-b from-background-dark via-background-dark to-surface-dark" />

                {/* Subtle Grid Pattern */}
                <div className="pointer-events-none absolute inset-0 [background-image:linear-gradient(rgba(76,255,0,0.3)_1px,transparent_1px),linear-gradient(90deg,rgba(76,255,0,0.3)_1px,transparent_1px)] [background-size:60px_60px] opacity-[0.02]" />

                {/* Top Accent Line */}
                <div className="pointer-events-none absolute top-0 right-0 left-0 h-px bg-gradient-to-r from-transparent via-primary/50 to-transparent" />

                {/* Main Container */}
                <div className="relative z-10 mx-auto flex w-full max-w-md flex-col">
                    {/* Top Section - Logo & Brand */}
                    <div className="flex flex-1 flex-col items-center justify-center px-8">
                        {/* Logo Mark */}
                        <div className="mb-8 flex flex-col items-center">
                            {/* Stylized Sumie Mark */}
                            <div className="relative mb-6 flex size-24 items-center justify-center">
                                {/* Outer ring */}
                                <div className="absolute inset-0 rounded-full border border-primary/20" />
                                {/* Inner ring with pulse */}
                                <div className="absolute inset-2 animate-pulse rounded-full border border-primary/40" />
                                {/* Center icon */}
                                <AppIcon name="menu_book" className="relative z-10 text-4xl text-primary" />
                                {/* Corner accent */}
                                <div className="absolute -top-1 -right-1 size-3 border-t border-r border-primary" />
                                <div className="absolute -bottom-1 -left-1 size-3 border-b border-l border-primary" />
                            </div>

                            {/* Brand Name */}
                            <div className="text-center">
                                <h1 className="mb-1 font-japanese text-[3rem] font-bold tracking-[0.15em] text-white">SUMIE</h1>
                                <div className="flex items-center justify-center gap-2 text-xs text-zinc-500">
                                    <span>墨絵</span>
                                    <span className="text-zinc-700">|</span>
                                    <span>Manga Reader</span>
                                </div>
                            </div>
                        </div>

                        {/* Tagline */}
                        <p className="max-w-[280px] text-center text-sm leading-relaxed text-zinc-400">
                            Your personal manga library.
                            <br />
                            <span className="text-zinc-500">Track, read, and discover.</span>
                        </p>
                    </div>

                    {/* Bottom Section - Form */}
                    <div className="px-8 pt-4 pb-12">
                        <Form {...onboardingStore.form()} className="flex w-full flex-col gap-5">
                            {({ processing, errors }) => (
                                <>
                                    {/* Input Field */}
                                    <div className="flex flex-col gap-2">
                                        <label className="text-xs font-bold tracking-[0.12em] text-zinc-500 uppercase" htmlFor="name">
                                            What should we call you?
                                        </label>
                                        <div className="relative">
                                            <input
                                                id="name"
                                                name="name"
                                                autoComplete="name"
                                                placeholder="Enter your name"
                                                maxLength={30}
                                                required
                                                autoFocus
                                                className="peer h-12 w-full rounded-lg border border-border-dark bg-surface-dark px-4 text-sm font-bold tracking-[0.08em] text-text-light uppercase transition-all outline-none placeholder:font-medium placeholder:tracking-[0.08em] placeholder:text-zinc-600 placeholder:uppercase focus:border-primary focus:ring-1 focus:ring-primary/20"
                                            />
                                            {/* Focus indicator line */}
                                            <div className="pointer-events-none absolute bottom-0 left-1/2 h-[2px] w-0 -translate-x-1/2 bg-primary transition-all duration-300 peer-focus:w-full" />
                                        </div>
                                        {errors.name && <p className="text-xs font-medium text-red-400">{errors.name}</p>}
                                    </div>

                                    {/* Submit Button */}
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="group relative flex h-12 w-full items-center justify-center rounded-lg bg-primary font-semibold text-black transition-all duration-200 hover:bg-primary/90 hover:shadow-[0_0_20px_rgba(76,255,0,0.3)] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-70"
                                    >
                                        <span className="flex items-center gap-2">
                                            {processing ? 'Creating account...' : 'Get Started'}
                                            {!processing && (
                                                <AppIcon
                                                    name="arrow_forward"
                                                    className="text-lg transition-transform duration-200 group-hover:translate-x-0.5"
                                                />
                                            )}
                                        </span>
                                    </button>

                                    {processing && (
                                        <div className="flex items-center justify-center gap-2 text-xs text-zinc-500">
                                            <AppIcon name="progress_activity" className="animate-spin text-sm" />
                                            Preparing your home feed...
                                        </div>
                                    )}
                                </>
                            )}
                        </Form>

                        {/* Minimal Footer */}
                        <div className="mt-8 flex flex-col items-center gap-3">
                            <div className="flex items-center gap-1.5">
                                <div className="size-1.5 rounded-full bg-primary/60" />
                                <p className="text-xs text-zinc-600">Ready when you are</p>
                            </div>

                            {/* Version */}
                            <p className="text-[10px] tracking-wider text-zinc-700">v1.0.0</p>
                        </div>
                    </div>
                </div>

                {/* Bottom Accent */}
                <div className="pointer-events-none absolute right-0 bottom-0 left-0 h-px bg-gradient-to-r from-transparent via-primary/30 to-transparent" />
            </div>
        </>
    );
}
