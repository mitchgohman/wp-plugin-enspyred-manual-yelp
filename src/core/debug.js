/**
 * Debug logging utilities for Enspyred Manual Yelp
 * Only logs to console when debug mode is enabled in plugin settings
 */

const isDebugEnabled = () => {
  return window.EMY_DATA?.debug === true;
};

export const debugError = (...args) => {
  if (isDebugEnabled()) {
    console.error('[EMY]', ...args);
  }
};
