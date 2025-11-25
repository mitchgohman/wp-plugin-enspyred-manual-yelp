import axiosObj from "axios";
import { debugError } from "./debug.js";

// Create axios instance with WordPress REST API configuration
const axios = axiosObj.create({
    baseURL: window.EMY_DATA?.root || "/wp-json/enspyred-manual-yelp/v1/",
    headers: {
        "Content-Type": "application/json",
    },
});

// Add request interceptor to dynamically inject WordPress nonce
axios.interceptors.request.use(
    (config) => {
        // Add WordPress nonce for authentication if available
        if (window.EMY_DATA?.nonce) {
            config.headers["X-WP-Nonce"] = window.EMY_DATA.nonce;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Add response interceptor for consistent error handling
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        // Enhanced error handling for WordPress REST API responses
        if (error.response?.status === 401) {
            debugError(
                "WordPress authentication failed - nonce may be expired"
            );
        } else if (error.response?.status === 403) {
            debugError("WordPress permission denied for this endpoint");
        }
        return Promise.reject(error);
    }
);

export default axios;
