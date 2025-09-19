import LocalData from "../utils/LocalData";


interface ILink {
    link: string;
    label: string;
}

class Header {
	private BASE_LINKS: ILink[] = [
		{ link: 'index.html', label: 'Início' },
		{ link: 'faq.html', label: 'Suporte' }
	];

	private LOGGED_LINKS: ILink[] = [
		...this.BASE_LINKS,
		{ link: 'dashboard.html', label: 'Meu Cofrinho' }
	];

	private UNLOGGED_LINKS: ILink[] = [
		...this.BASE_LINKS,
		{ link: 'login.html', label: 'Login' },
		{ link: 'register.html', label: 'Cadastre-se' }
	];

	private linksList: HTMLElement;

	constructor(linksList: HTMLElement = document.querySelector('ul.header-navigator__links-list') as HTMLElement) {
		this.linksList = linksList;
		const token: string = LocalData.get('token') as string;

		this.linksList.innerHTML = '';

		const createLinkCallback = (link: ILink) => {
			const li = document.createElement('li');
			li.classList.add('links-list__item');

			const url = link.link.split('.').length >= 2 ? link.link.split('.')[0] : '';

			li.innerHTML = `<a href="/${url}">${link.label}</a>`;
			linksList.appendChild(li);
		};

		if (!token) {
			this.UNLOGGED_LINKS.forEach(createLinkCallback);
			return;
		}

		this.LOGGED_LINKS.forEach(createLinkCallback);
	}
}

export default Header;
