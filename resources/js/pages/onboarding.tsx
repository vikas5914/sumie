import { Form, Head } from '@inertiajs/react';
import { store as onboardingStore } from '../routes/onboarding';

export default function Onboarding() {
    return (
        <>
            <Head title="System Entry" />
            <div className="flex min-h-screen flex-col items-center justify-center bg-background-dark font-mono text-text-light antialiased selection:bg-primary selection:text-black">
                <div className="relative mx-auto flex h-screen w-full max-w-md flex-col overflow-hidden border-x border-border-dark">
                    {/* Background Effects */}
                    <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_2px,3px_100%] opacity-[0.03]" />
                    <div className="pointer-events-none absolute top-0 left-1/2 h-[50vh] w-full -translate-x-1/2 rounded-full bg-primary/5 blur-[100px]" />

                    <div className="z-10 flex flex-1 flex-col items-center justify-center gap-8 p-8">
                        <div className="flex flex-col items-center gap-4 text-center">
                            <div className="mb-4 flex size-16 items-center justify-center border border-primary bg-primary/10 shadow-[4px_4px_0_0_#4CFF00]">
                                <span className="material-symbols-outlined text-4xl text-primary">terminal</span>
                            </div>
                            <h1 className="text-4xl font-bold tracking-tighter text-white uppercase">
                                SUMIE<span className="text-primary">_OS</span>
                            </h1>
                            <p className="text-xs font-bold tracking-[0.2em] text-zinc-500 uppercase">Manga Reading Terminal v2.0</p>
                        </div>

                        <div className="flex w-full max-w-xs flex-col gap-4">
                            <div className="flex flex-col gap-2 border border-border-dark bg-surface-dark p-4">
                                <div className="flex items-center justify-between text-[10px] font-bold text-zinc-600 uppercase">
                                    <span>System Status</span>
                                    <span className="blink text-primary">ONLINE</span>
                                </div>
                                <div className="h-0.5 w-full overflow-hidden bg-border-dark">
                                    <div className="h-full w-full animate-pulse bg-primary" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-[10px] text-zinc-500 uppercase">&gt; Loading Modules... OK</p>
                                    <p className="text-[10px] text-zinc-500 uppercase">&gt; Syncing Library... OK</p>
                                    <p className="text-[10px] text-zinc-500 uppercase">&gt; Establishing Link... OK</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="z-10 p-8 pb-12">
                        <Form {...onboardingStore.form()} className="flex w-full flex-col gap-4">
                            {({ processing, errors }) => (
                                <>
                                    <div className="flex flex-col gap-2">
                                        <label className="text-[10px] font-bold tracking-[0.2em] text-zinc-500 uppercase" htmlFor="name">
                                            Operator Name
                                        </label>
                                        <input
                                            id="name"
                                            name="name"
                                            autoComplete="name"
                                            placeholder="Enter your call sign"
                                            maxLength={30}
                                            required
                                            autoFocus
                                            className="h-12 w-full border border-border-dark bg-surface-dark px-4 text-sm font-bold text-text-light uppercase outline-none transition-colors focus:border-primary focus:ring-0"
                                        />
                                        {errors.name && <p className="text-[10px] font-bold text-red-400 uppercase">{errors.name}</p>}
                                    </div>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="group relative flex h-14 w-full items-center justify-center bg-primary font-bold tracking-widest text-black uppercase shadow-[4px_4px_0_0_#333] transition-all hover:bg-white hover:shadow-[4px_4px_0_0_#fff] disabled:cursor-not-allowed disabled:opacity-80 active:translate-y-0.5 active:shadow-none"
                                    >
                                        <span className="relative z-10 flex items-center gap-2">
                                            {processing ? 'Initializing...' : 'Initialize System'}
                                            <span className="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">
                                                arrow_forward
                                            </span>
                                        </span>
                                    </button>
                                </>
                            )}
                        </Form>
                        <p className="mt-4 text-center text-[10px] font-bold text-zinc-600 uppercase">By entering you accept the protocol</p>
                    </div>
                </div>
            </div>
        </>
    );
}
