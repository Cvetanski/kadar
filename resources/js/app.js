import './bootstrap';

// Livewire bundles and boots its own copy of Alpine. Starting a second,
// separate Alpine instance here (as Breeze's default app.js does) races
// Livewire's boot: whichever Alpine runs first claims every x-data subtree
// it walks, so when Livewire's own Alpine boots second it skips those
// subtrees as "already initialized" — silently breaking hydration for any
// Livewire component nested inside one (e.g. the unread-badge instances
// inside <nav x-data="...">, which never became interactive because of
// this exact conflict). Don't import/start Alpine manually.

// Short two-tone chime played when a new message arrives, similar to other
// chat apps. Synthesized via Web Audio API so no external audio asset is
// needed. Ignores failures silently — playback can be blocked by browser
// autoplay policy before the user has interacted with the page, and a
// missing sound isn't worth surfacing an error for.
function playMessageSound() {
    try {
        window.__messageAudioCtx = window.__messageAudioCtx || new (window.AudioContext || window.webkitAudioContext)();
        const ctx = window.__messageAudioCtx;

        const chime = () => {
            const now = ctx.currentTime;

            const tone = (freq, start, duration) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(0, now + start);
                gain.gain.linearRampToValueAtTime(0.2, now + start + 0.01);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + start + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now + start);
                osc.stop(now + start + duration);
            };

            tone(880, 0, 0.12);
            tone(1318.51, 0.09, 0.18);
        };

        if (ctx.state === 'suspended') {
            ctx.resume().then(chime).catch(() => {});
        } else {
            chime();
        }
    } catch (e) {
        //
    }
}

document.addEventListener('livewire:init', () => {
    Livewire.on('new-message-received', () => playMessageSound());
});
