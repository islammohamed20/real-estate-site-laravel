/**
 * Dashboard history fix — global form interceptor
 *
 * Intercepts POST form submissions across the dashboard and submits them
 * via fetch. For successful redirects it uses window.location.replace() so
 * the POST and 302/303 redirect do not pile up in the browser history.
 * For validation/error responses the returned HTML is written into the page
 * so the user sees the form errors without adding extra history entries.
 */

document.addEventListener('submit', function (e) {
    const form = e.target;

    // Only handle POST forms, ignore forms explicitly opted out
    if (form.method.toLowerCase() !== 'post') return;
    if (form.hasAttribute('data-no-ajax')) return;

    // Skip forms that open a new tab/window or download
    if (form.getAttribute('target') === '_blank') return;
    if (form.getAttribute('data-download') !== null) return;

    e.preventDefault();

    const submitter = e.submitter;
    // `HTMLButtonElement.formAction` resolves to the current page URL even when
    // the button has no `formaction` attribute. Only let a submit button
    // override the form action when the attribute was explicitly provided.
    const action = submitter?.hasAttribute('formaction')
        ? submitter.formAction
        : (form.action || window.location.href);
    const method = submitter?.hasAttribute('formmethod')
        ? submitter.formMethod
        : form.method;
    const formData = submitter ? new FormData(form, submitter) : new FormData(form);

    fetch(action, {
        method: method || 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        },
        redirect: 'follow',
    })
        .then(async response => {
            const finalUrl = response.url;

            if (response.ok && response.status < 400) {
                // Successful redirect: replace current history with final URL
                // This prevents the POST step and the 302 step from appearing
                // in the browser's back/forward history.
                if (finalUrl !== window.location.href) {
                    window.location.replace(finalUrl);
                } else {
                    window.location.reload();
                }
                return;
            }

            // Validation / error: show the returned HTML at the same URL
            const html = await response.text();
            if (html.trim().length > 0) {
                document.open();
                document.write(html);
                document.close();
                if (window.history.replaceState && finalUrl !== window.location.href) {
                    window.history.replaceState(null, '', finalUrl);
                }
            } else {
                window.location.reload();
            }
        })
        .catch(err => {
            console.error('[dashboard-form] AJAX submission failed, falling back to normal submit:', err);
            form.submit();
        });
});

// Also use replace() for client-side navigation helpers where possible.
window.__dashboardNavigateReplace = function (url) {
    if (url) window.location.replace(url);
};
