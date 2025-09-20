import UserSession from "../models/UserSession";
import UIComponent from "./UIComponent";

interface ILink {
    link: string;
    label: string;
}

class HeaderLinks extends UIComponent {
	private BASE_LINKS: ILink[] = [
		{ link: 'index.html', label: 'Início' },
		{ link: 'faq.html', label: 'Suporte' }
	];

	private LOGGED_LINKS: ILink[] = [
		...this.BASE_LINKS,
		{ link: 'dashboard.html', label: 'Painél de Controle' }
	];

	private UNLOGGED_LINKS: ILink[] = [
		...this.BASE_LINKS,
		{ link: 'login.html', label: 'Login' },
		{ link: 'register.html', label: 'Cadastre-se' }
	];

	protected identifierClass: string = 'header-navigator__links-list';

	constructor() {
		super();
		this.createElement();
	}

	public appendInHeader() {
		const header = document.querySelector('header nav#header-navigator') as HTMLElement;
		header.innerHTML = '';
		header.appendChild(this.element as Node);
	}

	private createLinkElement(link: ILink): HTMLLIElement {
		const li: HTMLLIElement = document.createElement('li');
		li.classList.add('links-list__item');

		const url = link.link.split('.')[0];

		li.innerHTML = `<a href="/${url}">${link.label}</a>`;
		return li;
	}

	protected createElement() {
		const ul = document.createElement('ul') as HTMLUListElement;
		ul.classList.add(this.identifierClass);

		if (UserSession.isLogged()) {
			this.LOGGED_LINKS.forEach(l => {
				const link: HTMLLIElement = this.createLinkElement(l);
				ul.appendChild(link);
			});

			this.element = ul;
			return;
		}

		this.UNLOGGED_LINKS.forEach(l => {
			const link: HTMLLIElement = this.createLinkElement(l);
			ul.appendChild(link);
		});

		this.element = ul;
	}
}

export default HeaderLinks;
