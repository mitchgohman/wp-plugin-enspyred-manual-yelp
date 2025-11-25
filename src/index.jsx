import { createRoot } from "react-dom/client";
import axios from "./core/axios.js";
import { debugError } from "./core/debug.js";

import EnspyredYelpReviews from "./EnspyredYelpReviews.jsx";

const fetchReviews = async (gallery, branch, limit) => {
    try {
        let url = `reviews?gallery=${encodeURIComponent(gallery)}`;
        if (branch) url += `&branch=${encodeURIComponent(branch)}`;
        if (limit) url += `&limit=${encodeURIComponent(limit)}`;

        const response = await axios.get(url);
        return response.data;
    } catch (error) {
        throw new Error(
            `Reviews load failed (${error.response?.status || "network error"})`
        );
    }
};

const mountAll = () => {
    const nodes = document.querySelectorAll(".enspyred-manual-yelp");
    nodes.forEach(async (el) => {
        if (el.__emy_mounted) return;
        el.__emy_mounted = true;

        const gallery = el.dataset.emyGallery;
        const branch = el.dataset.emyBranch || '';
        const limit = el.dataset.emyLimit || '';

        const root = createRoot(el);
        root.render(<div aria-live="polite">Loading reviews…</div>);

        try {
            const resp = await fetchReviews(gallery, branch, limit);

            root.render(
                <EnspyredYelpReviews
                    reviews={resp.reviews}
                    gallery={gallery}
                />
            );
        } catch (err) {
            debugError(err);
            root.render(
                <div aria-live="polite" style={{ color: "crimson" }}>
                    Failed to load reviews for gallery: {gallery}
                </div>
            );
        }
    });
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", mountAll);
} else {
    mountAll();
}

// Support Vite HMR during development
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        mountAll();
    });
}
