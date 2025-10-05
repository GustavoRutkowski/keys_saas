import HeaderLinks from "./components/HeaderLinks";
new HeaderLinks().appendInHeader();

const sections = document.querySelectorAll('section.landing-page__section') as NodeListOf<HTMLElement>;

const handleSectionFadeIn: IntersectionObserverCallback = (entries, observer) => {
    entries.forEach(entry => {
        if (!entry.isIntersecting) return;

        entry.target.classList.add('fade-in');
        observer.unobserve(entry.target);
    });
};

const observer: IntersectionObserver = new IntersectionObserver(
    handleSectionFadeIn, { threshold: 0.15 } // Precisa de 15% de visibilidade para aparecer
);

sections.forEach(section => observer.observe(section));
