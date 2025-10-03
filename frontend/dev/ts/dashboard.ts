import HeaderLinks from './components/HeaderLinks';
import TabsMenu from './components/TabsMenu';
import IResponse from './interfaces/IResponse';
import IUserData from './interfaces/IUser';
import UserSession from './models/UserSession';

const user = await UserSession.authenticate() as IUserData;
new HeaderLinks().appendInHeader();

// Insere o LoaderMenu

const panelSelectedSection = document.querySelector('section#panel-selected') as HTMLElement;

const tabsMenu = new TabsMenu(panelSelectedSection, {
    defaultItem: 'passwords',
    storageKey: 'dashboard__panel-selected',
    items: [
        {
            id: 'passwords', label: 'Senhas', faIcon: 'fa-key',
            path: '../../public/panels/app/_passwords.html', action: () => console.log('passwords')
        },
        {
            id: 'cards', label: 'Cartões', faIcon: 'fa-credit-card',
            path: '../../public/panels/app/_cards.html', action: () => console.log('cards')
        },
        {
            id: 'documents', label: 'Documentos Digitalizados', faIcon: 'fa-id-card',
            path: '../../public/panels/app/_documents.html', action: () => console.log('docuemnts')
        }
    ],
    options: {
        menuClass: 'dashboard-links',
        itemClass: 'dashboard-links__item',
        selectedClass: 'dashboard-links__item--active'
    }
});


const aside = document.querySelector('aside.lateral-bar') as HTMLElement;
const userSection = document.querySelector('section.lateral-bar__user-section') as HTMLElement;

aside.insertBefore(tabsMenu.getElement() as Node, userSection);


// Insere as informações do usuário

const pictureImg = document.querySelector('.user-infos__user_picture > img') as HTMLImageElement;
const nicknameTxt = document.querySelector('.user-infos__nickname') as HTMLSpanElement;

if (user.picture) pictureImg.src = `../public/imgs/upload/${user.picture}`;
nicknameTxt.textContent = user.name;

// Logout Button

const logoutBtn = document.querySelector('button#user-section__logout-btn') as HTMLButtonElement;
logoutBtn.addEventListener('click', UserSession.logout);
