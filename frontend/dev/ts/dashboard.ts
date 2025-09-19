import Header from './components/Header';
import LoaderMenu from "./components/LoaderMenu";
import User from './models/User';
import LocalData from './utils/LocalData';
customElements.define('loader-menu', LoaderMenu);

new Header();

// Insere o LoaderMenu

const loaderMenu = document.createElement('loader-menu') as LoaderMenu;

loaderMenu.setup({
    target: 'section#panel-selected',
    default: 'passwords',
    
    items: [
        {
            id: 'passwords', label: 'Senhas',
            path: '../../public/panels/app/_passwords.html', action: () => console.log('a')
        },
        {
            id: 'cards', label: 'Cartões',
            path: '../../public/panels/app/_cards.html', action: () => console.log('b')
        },
        {
            id: 'documents', label: 'Documentos Digitalizados',
            path: '../../public/panels/app/_documents.html', action: () => console.log('c')
        }
    ]
});

const aside = document.querySelector('aside.lateral-bar') as HTMLElement;
const userSection = document.querySelector('section.lateral-bar__user-section') as HTMLElement;

aside.insertBefore(loaderMenu, userSection)

// Insere as informações do usuário

const { data: user } = await User.get();

const pictureImg = document.querySelector('.user-infos__user_picture > img') as HTMLImageElement;
const nicknameTxt = document.querySelector('.user-infos__nickname') as HTMLSpanElement;

if (user.picture) pictureImg.src = `../public/imgs/upload/${user.picture}`;
nicknameTxt.textContent = user.name;

// Logout Button

const logoutBtn = document.querySelector('button#user-section__logout-btn') as HTMLButtonElement;

logoutBtn.addEventListener('click', () => {
    LocalData.remove('token');
    location.href = '/login';
});
