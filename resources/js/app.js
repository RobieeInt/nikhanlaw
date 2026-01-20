import "./bootstrap";
import collapse from "@alpinejs/collapse";

// Theme helper (tidak tergantung Alpine)
window.Theme = window.Theme || {
    get() {
        const saved = localStorage.getItem("theme");
        if (saved === "dark" || saved === "light") return saved;
        return window.matchMedia &&
            window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light";
    },
    apply(theme) {
        document.documentElement.classList.toggle("dark", theme === "dark");
    },
    toggle() {
        const next = this.get() === "dark" ? "light" : "dark";
        localStorage.setItem("theme", next);
        this.apply(next);
        return next;
    },
};

// apply theme on load
window.Theme.apply(window.Theme.get());

// ✅ Alpine ada dari Livewire, jadi kita cuma nunggu Alpine init lalu pasang plugin
document.addEventListener("alpine:init", () => {
    // Livewire expose Alpine ke window
    if (window.Alpine) {
        window.Alpine.plugin(collapse);
    }
});
