import ChangeMainPassModal from './components/ChangeMainPassModal';
import HeaderLinks from './components/HeaderLinks';
import ToggleViewButton from './components/ToggleViewButton';
import IFile from './interfaces/IFile';
import IResponse from './interfaces/IResponse';
import IUserData, { IUserUpdateInfos } from './interfaces/IUser';
import User from './models/User';
import UserSession from './models/UserSession';
import toBase64 from './utils/toBase64';

ToggleViewButton.createAllButtons();

const user = await UserSession.authenticate() as IUserData;
new HeaderLinks().appendInHeader();

// Editar Informações:

const updateInfos: IUserUpdateInfos = {
    name: user.name,
    picture: null,
}

const nicknameTxt = document.querySelector('label.user-nickname__nickname') as HTMLLabelElement;
nicknameTxt.textContent = user.name;

nicknameTxt.addEventListener('focusout', () => {
    nicknameTxt.contentEditable = 'false';
});

const editNicknameBtn = document.querySelector('button#edit-nickname-btn') as HTMLButtonElement;
editNicknameBtn.addEventListener('click', () => {
    nicknameTxt.contentEditable = 'true';
    nicknameTxt.focus();
});

nicknameTxt.addEventListener('input', () => updateInfos.name = nicknameTxt.textContent);

const pictureImg = document.querySelector('label.user-infos__picture > img') as HTMLImageElement;
if (user.picture) pictureImg.src = `../public/imgs/upload/${user.picture}`;

const pictureInput = document.querySelector('input#user-picture-input') as HTMLInputElement;

pictureInput.addEventListener('change', async () => {
    const imgFile = pictureInput.files?.[0] as File | null;
    if (!imgFile) return;

    const base64 = await toBase64(imgFile) as string;
    pictureImg.src = base64;

    const picture: IFile = {
        filename: imgFile.name,
        data: base64.split(',')[1] // Remove o prefixo
    };
    
    updateInfos.picture = picture;
});

const saveChangesBtn = document.querySelector('button#save-changes-btn') as HTMLButtonElement;
saveChangesBtn.addEventListener('click', async e => {
    e.preventDefault();
    const res: IResponse = await User.updateInfos(updateInfos);

    if (res.success) alert('Atualizado com sucesso');
    else alert(res.message);
});


// Change Password Modal:

const modal = new ChangeMainPassModal();
ToggleViewButton.createAllButtons(modal.getElement() as ParentNode);

const changePasswordBtn = document.querySelector('button#change-password-btn') as HTMLButtonElement;
changePasswordBtn.addEventListener('click', () => modal.show());
