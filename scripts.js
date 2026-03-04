document.addEventListener('DOMContentLoaded', () => {

    /* ========================
       FAQ Toggle (SAFE)
    ======================== */
    const faqItems = document.querySelectorAll('.bt-faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.bt-faq-question');
        if (!question) return;

        question.addEventListener('click', () => {
            const isOpen = item.classList.contains('bt-active');

            // Close all
            faqItems.forEach(faq => {
                faq.classList.remove('bt-active');

                const answer = faq.querySelector('.bt-faq-answer');
                const icon = faq.querySelector('i');

                if (answer) answer.style.display = 'none';
                if (icon) icon.style.transform = 'rotate(0deg)';
            });

            // Open current
            if (!isOpen) {
                item.classList.add('bt-active');

                const answer = item.querySelector('.bt-faq-answer');
                const icon = item.querySelector('i');

                if (answer) answer.style.display = 'block';
                if (icon) icon.style.transform = 'rotate(180deg)';
            }
        });
    });


    /* ========================
       Mobile Menu (SAFE)
    ======================== */
    const mobileToggle = document.querySelector('.bt-mobile-toggle');
    const navLinks = document.querySelector('.bt-nav-links');

    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', () => {
            mobileToggle.classList.toggle('bt-active');
            navLinks.classList.toggle('bt-active');

            document.body.style.overflow =
                navLinks.classList.contains('bt-active') ? 'hidden' : '';
        });

        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileToggle.classList.remove('bt-active');
                navLinks.classList.remove('bt-active');
                document.body.style.overflow = '';
            });
        });
    }


    /* ========================
       Smooth Scroll
    ======================== */
    document.addEventListener('click', (e) => {
        const anchor = e.target.closest('a[href*="#"]');
        if (!anchor) return;

        const href = anchor.getAttribute('href');
        const targetId = href.substring(href.indexOf('#'));

        if (targetId === '#') return;

        const targetElement = document.querySelector(targetId);
        if (!targetElement) return;

        e.preventDefault();

        const navElement =
            document.querySelector('.bt-navbar') ||
            document.querySelector('.navbar');

        const navHeight = navElement ? navElement.offsetHeight : 0;

        const targetPosition =
            targetElement.getBoundingClientRect().top +
            window.pageYOffset -
            navHeight;

        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    });


    /* ========================
       Header Scroll (SAFE)
    ======================== */
    const header = document.querySelector('.bt-navbar');

    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.style.padding = '10px 0';
                header.style.background = 'rgba(255,255,255,0.98)';
            } else {
                header.style.padding = '15px 0';
                header.style.background = 'rgba(255,255,255,0.9)';
            }
        });
    }


    /* ========================
       Tabs
    ======================== */
    const tabBtns = document.querySelectorAll('.bt-tab-btn');
    const tabContents = document.querySelectorAll('.bt-tab-content');

    if (tabBtns.length && tabContents.length) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.getAttribute('data-tab');

                tabBtns.forEach(b => b.classList.remove('bt-active'));
                btn.classList.add('bt-active');

                tabContents.forEach(content => {
                    content.classList.toggle(
                        'bt-active',
                        content.getAttribute('id') === tabId
                    );
                });
            });
        });
    }

});
