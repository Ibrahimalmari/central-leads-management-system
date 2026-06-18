<style>
    :root {
        --cl-surface: rgba(255, 255, 255, 0.92);
        --cl-border: rgba(15, 23, 42, 0.10);
        --cl-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
        --cl-dark-surface: rgba(15, 23, 42, 0.94);
        --cl-dark-border: rgba(148, 163, 184, 0.18);
        --cl-dark-shadow: 0 22px 55px rgba(2, 6, 23, 0.38);
    }

    html[dir="rtl"] body {
        letter-spacing: 0;
    }

    .fi-sidebar {
        border-inline-end: 1px solid var(--cl-border);
    }

    .fi-sidebar .fi-logo,
    .fi-sidebar-header .fi-logo,
    .fi-topbar-header .fi-logo,
    .fi-topbar .fi-logo {
        width: 10.5rem !important;
        height: 3rem !important;
        max-width: 100%;
        border-radius: 6px;
        object-fit: cover;
        object-position: center;
    }

    .fi-sidebar a:has(.fi-logo),
    .fi-sidebar-header a:has(.fi-logo),
    .fi-topbar-header a:has(.fi-logo),
    .fi-topbar a:has(.fi-logo) {
        display: inline-flex;
        align-items: center;
        min-height: 3.25rem;
        overflow: hidden;
    }

    .fi-topbar nav,
    .fi-sidebar,
    .fi-section,
    .fi-wi-widget .fi-section,
    .fi-ta-ctn {
        box-shadow: var(--cl-shadow);
    }

    .fi-section,
    .fi-wi-widget .fi-section,
    .fi-ta-ctn {
        border-color: var(--cl-border);
        border-radius: 8px;
        background: var(--cl-surface);
    }

    .cl-dashboard-filters {
        overflow: hidden;
        border-color: rgba(37, 99, 235, 0.14);
        background:
            linear-gradient(135deg, rgba(13, 148, 136, 0.07), transparent 36%),
            linear-gradient(315deg, rgba(37, 99, 235, 0.08), transparent 38%),
            var(--cl-surface);
    }

    .cl-dashboard-filters .fi-section-header {
        align-items: center;
        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
        background: rgba(248, 250, 252, 0.44);
    }

    .cl-dashboard-filters .fi-section-content {
        gap: .9rem;
    }

    .fi-simple-layout {
        min-height: 100dvh;
        align-items: center;
        background:
            linear-gradient(135deg, rgba(13, 148, 136, 0.10), transparent 34%),
            linear-gradient(315deg, rgba(37, 99, 235, 0.12), transparent 34%),
            #f8fafc;
    }

    .fi-simple-main-ctn {
        min-height: 100dvh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .fi-simple-main {
        width: min(100%, 34rem);
        padding: 0 !important;
    }

    .fi-simple-page-content {
        gap: .9rem;
    }

    .fi-simple-header {
        gap: .8rem;
        margin-bottom: .25rem;
    }

    .fi-simple-header a:has(.fi-logo) {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 6.25rem;
        overflow: visible;
    }

    .fi-simple-header .fi-logo {
        width: 18rem !important;
        height: 5.75rem !important;
        max-width: min(100%, 18rem);
        border-radius: 8px;
        object-fit: contain;
        object-position: center;
    }

    .fi-simple-header-heading {
        font-size: clamp(1.55rem, 2.2vw, 2rem);
        line-height: 1.35;
    }

    .fi-simple-page-content > .fi-sc {
        border: 1px solid var(--cl-border);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.88);
        padding: 1.15rem;
        box-shadow: var(--cl-shadow);
        gap: .9rem;
    }

    .cl-auth-intro {
        margin-bottom: .15rem;
        border: 0;
        border-radius: 8px;
        background: linear-gradient(135deg, rgba(13, 148, 136, 0.10), rgba(37, 99, 235, 0.08));
        padding: .85rem 1rem;
        box-shadow: none;
    }

    .cl-auth-intro__eyebrow {
        margin: 0 0 .35rem;
        color: #0f766e;
        font-size: .74rem;
        font-weight: 700;
    }

    .cl-auth-intro__title {
        margin: 0;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.45;
    }

    .cl-auth-intro__body {
        margin: .3rem 0 0;
        color: #475569;
        font-size: .86rem;
        line-height: 1.65;
    }

    .fi-simple-page .fi-sc-form,
    .fi-simple-page .fi-sc-form > .fi-sc,
    .fi-simple-page .fi-sc-form .fi-sc {
        gap: .82rem;
    }

    .fi-simple-page .fi-input-wrp {
        border-radius: 8px;
        min-height: 2.75rem;
    }

    .fi-simple-page .fi-btn {
        min-height: 2.8rem;
        border-radius: 8px;
        font-weight: 700;
    }

    .cl-auth-language {
        display: flex;
        justify-content: center;
        gap: .5rem;
        margin-top: .75rem;
    }

    .cl-auth-language a {
        min-width: 5.75rem;
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.68);
        padding: .45rem .9rem;
        color: #475569;
        font-size: .82rem;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
    }

    .cl-auth-language a.is-active {
        border-color: rgba(37, 99, 235, 0.26);
        background: rgba(37, 99, 235, 0.10);
        color: #1d4ed8;
    }

    .cl-lead-section {
        overflow: hidden;
    }

    .cl-lead-section .fi-section-header {
        min-height: 4.35rem;
        align-items: center;
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(248, 250, 252, 0.56);
    }

    .cl-lead-section .fi-section-content {
        gap: 1.05rem;
    }

    .cl-lead-section--summary .fi-section-content {
        align-items: start;
    }

    .cl-lead-details-grid {
        align-items: start;
    }

    .cl-lead-section--contact,
    .cl-lead-section--follow {
        height: 100%;
    }

    .cl-lead-section--contact .fi-section-content,
    .cl-lead-section--follow .fi-section-content {
        align-content: start;
    }

    .cl-lead-message {
        border: 1px solid rgba(37, 99, 235, 0.16);
        border-radius: 8px;
        background: rgba(248, 251, 255, 0.92);
        padding: 1rem 1.1rem;
        color: #0f172a;
        line-height: 1.9;
        white-space: pre-wrap;
    }

    .cl-extra-data__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(18rem, 1fr));
        gap: .8rem;
    }

    .cl-extra-data__item {
        min-width: 0;
        border: 1px solid rgba(148, 163, 184, 0.20);
        border-radius: 8px;
        background: rgba(248, 250, 252, 0.74);
        padding: .9rem 1rem;
    }

    .cl-extra-data__item--wide {
        grid-column: 1 / -1;
    }

    .cl-extra-data__label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
    }

    .cl-extra-data__label code {
        max-width: 45%;
        overflow: hidden;
        color: #64748b;
        font-size: .72rem;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cl-extra-data__value {
        margin-top: .55rem;
        overflow-wrap: anywhere;
        color: #0f172a;
        font-size: .95rem;
        line-height: 1.75;
    }

    .cl-extra-data__value a {
        color: #155eef;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .cl-extra-data__empty {
        border: 1px dashed rgba(148, 163, 184, 0.36);
        border-radius: 8px;
        background: rgba(248, 250, 252, 0.72);
        padding: 1rem;
        color: #64748b;
    }

    .cl-extra-data__muted {
        color: #94a3b8;
    }

    .cl-docs {
        display: grid;
        gap: 1rem;
    }

    .cl-docs__hero,
    .cl-docs__panel,
    .cl-docs__grid > article {
        border: 1px solid var(--cl-border);
        border-radius: 8px;
        background: var(--cl-surface);
        box-shadow: var(--cl-shadow);
    }

    .cl-docs__hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem;
    }

    .cl-docs__hero p {
        margin: 0 0 .3rem;
        color: #0f766e;
        font-size: .8rem;
        font-weight: 800;
    }

    .cl-docs__hero h2 {
        margin: 0;
        color: #0f172a;
        font-size: 1.35rem;
        font-weight: 800;
    }

    .cl-docs__hero span {
        display: block;
        margin-top: .35rem;
        color: #64748b;
        line-height: 1.7;
    }

    .cl-docs code {
        direction: ltr;
        unicode-bidi: plaintext;
    }

    .cl-docs__hero > code {
        border: 1px solid rgba(37, 99, 235, 0.16);
        border-radius: 8px;
        background: rgba(37, 99, 235, 0.08);
        padding: .65rem .8rem;
        color: #1d4ed8;
        font-weight: 700;
        max-width: min(100%, 32rem);
        overflow: auto hidden;
        white-space: nowrap;
    }

    .cl-docs__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(20rem, 1fr));
        gap: 1rem;
    }

    .cl-docs__grid > article,
    .cl-docs__panel {
        padding: 1.1rem;
    }

    .cl-docs h3 {
        margin: 0 0 .85rem;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .cl-docs ol,
    .cl-docs ul {
        margin: 0;
        padding-inline-start: 1.35rem;
        color: #334155;
        line-height: 1.9;
    }

    .cl-docs__sites {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 22rem), 1fr));
        gap: .8rem;
    }

    .cl-docs__site {
        display: grid;
        gap: .9rem;
        min-width: 0;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 8px;
        background: rgba(248, 250, 252, 0.72);
        padding: 1rem;
    }

    .cl-docs__site-head {
        display: grid;
        gap: .2rem;
        min-width: 0;
    }

    .cl-docs__site-head strong {
        overflow: hidden;
        color: #0f172a;
        font-size: .98rem;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cl-docs__site-head span,
    .cl-docs__muted {
        color: #64748b;
    }

    .cl-docs__site dl {
        display: grid;
        gap: .65rem;
        margin: 0;
    }

    .cl-docs__site dl > div {
        display: grid;
        gap: .35rem;
        min-width: 0;
    }

    .cl-docs__site dt {
        color: #475569;
        font-size: .74rem;
        font-weight: 800;
    }

    .cl-docs__site dd {
        min-width: 0;
        margin: 0;
    }

    .cl-docs__site code {
        display: block;
        width: 100%;
        max-width: 100%;
        overflow: auto hidden;
        border: 1px solid rgba(37, 99, 235, 0.14);
        border-radius: 8px;
        background: rgba(37, 99, 235, 0.07);
        padding: .6rem .7rem;
        color: #1d4ed8;
        font-size: .78rem;
        font-weight: 700;
        line-height: 1.55;
        white-space: nowrap;
    }

    .cl-docs pre {
        max-height: 28rem;
        overflow: auto;
        border: 1px solid rgba(15, 23, 42, 0.10);
        border-radius: 8px;
        background: #020617;
        padding: 1rem;
        color: #e2e8f0;
        font-size: .78rem;
        line-height: 1.7;
    }

    .cl-docs pre code {
        display: block;
        min-width: max-content;
        white-space: pre;
    }

    .dark .fi-body,
    .dark .fi-simple-layout {
        background:
            linear-gradient(135deg, rgba(20, 184, 166, 0.14), transparent 35%),
            linear-gradient(315deg, rgba(59, 130, 246, 0.16), transparent 35%),
            #020617;
        color: #e2e8f0;
    }

    .dark .fi-topbar nav,
    .dark .fi-sidebar,
    .dark .fi-section,
    .dark .fi-wi-widget .fi-section,
    .dark .fi-ta-ctn,
    .dark .fi-simple-page-content > .fi-sc {
        border-color: var(--cl-dark-border);
        background: var(--cl-dark-surface);
        box-shadow: var(--cl-dark-shadow);
    }

    .dark .fi-input-wrp {
        border-color: rgba(148, 163, 184, 0.24);
        background: rgba(2, 6, 23, 0.68);
    }

    .dark .fi-input {
        color: #f8fafc;
    }

    .dark .fi-input::placeholder {
        color: #94a3b8;
    }

    .dark .cl-dashboard-filters {
        border-color: rgba(96, 165, 250, 0.20);
        background:
            linear-gradient(135deg, rgba(20, 184, 166, 0.13), transparent 36%),
            linear-gradient(315deg, rgba(59, 130, 246, 0.13), transparent 38%),
            var(--cl-dark-surface);
    }

    .dark .cl-dashboard-filters .fi-section-header {
        border-bottom-color: rgba(148, 163, 184, 0.14);
        background: rgba(15, 23, 42, 0.40);
    }

    .dark .cl-lead-section .fi-section-header {
        border-bottom-color: rgba(148, 163, 184, 0.14);
        background: rgba(15, 23, 42, 0.42);
    }

    .dark .cl-lead-message {
        border-color: rgba(96, 165, 250, 0.22);
        background: rgba(15, 23, 42, 0.68);
        color: #e2e8f0;
    }

    .dark .cl-extra-data__item,
    .dark .cl-extra-data__empty {
        border-color: rgba(148, 163, 184, 0.18);
        background: rgba(2, 6, 23, 0.36);
    }

    .dark .cl-extra-data__label {
        color: #cbd5e1;
    }

    .dark .cl-extra-data__label code,
    .dark .cl-extra-data__empty,
    .dark .cl-extra-data__muted {
        color: #94a3b8;
    }

    .dark .cl-extra-data__value {
        color: #f8fafc;
    }

    .dark .cl-docs__hero,
    .dark .cl-docs__panel,
    .dark .cl-docs__grid > article {
        border-color: var(--cl-dark-border);
        background: var(--cl-dark-surface);
        box-shadow: var(--cl-dark-shadow);
    }

    .dark .cl-docs__hero h2,
    .dark .cl-docs h3 {
        color: #f8fafc;
    }

    .dark .cl-docs__hero span,
    .dark .cl-docs ol,
    .dark .cl-docs ul,
    .dark .cl-docs__site-head strong {
        color: #f8fafc;
    }

    .dark .cl-docs__site-head span,
    .dark .cl-docs__muted {
        color: #cbd5e1;
    }

    .dark .cl-docs__site {
        border-color: rgba(148, 163, 184, 0.18);
        background: rgba(2, 6, 23, 0.36);
    }

    .dark .cl-docs__site dt {
        color: #cbd5e1;
    }

    .dark .cl-auth-intro {
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.16), rgba(59, 130, 246, 0.14));
        border-color: transparent;
    }

    .dark .cl-auth-intro__title {
        color: #f8fafc;
    }

    .dark .cl-auth-intro__body {
        color: #cbd5e1;
    }

    .dark .cl-auth-language a {
        border-color: rgba(148, 163, 184, 0.20);
        background: rgba(15, 23, 42, 0.58);
        color: #cbd5e1;
    }

    .dark .cl-auth-language a.is-active {
        border-color: rgba(96, 165, 250, 0.32);
        background: rgba(37, 99, 235, 0.18);
        color: #bfdbfe;
    }

    @media (max-height: 760px) {
        .fi-simple-main-ctn {
            align-items: flex-start;
            padding-block: .75rem;
        }

        .fi-simple-header {
            gap: .5rem;
        }

        .fi-simple-header .fi-logo {
            width: 15rem !important;
            height: 4.75rem !important;
        }

        .fi-simple-header-heading {
            font-size: 1.55rem;
        }

        .fi-simple-page-content > .fi-sc {
            padding: .9rem;
            gap: .7rem;
        }

        .cl-auth-intro {
            padding: .75rem .9rem;
        }

        .cl-auth-intro__body {
            display: none;
        }
    }
</style>
