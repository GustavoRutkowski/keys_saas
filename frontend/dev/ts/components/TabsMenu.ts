import UIComponent from "./UIComponent"

interface ITabsMenuOptions {
    menuClass?: string
    itemClass?: string
    selectedClass?: string
    iconClass?: string
    notifyClass?: string
    notifyActiveClass?: string
    frameClass?: string
}

interface ITabsMenuItem {
    id: string
    notify?: boolean
    label?: string
    faIcon?: string
    path?: string
    action?: () => void
}

interface ITabsMenuConfig {
    items: ITabsMenuItem[]
    defaultItem?: string
    storageKey?: string
    options?: ITabsMenuOptions
}

class TabsMenu extends UIComponent {
    protected identifierClass: string = '';

    private target: HTMLElement;

    private items: ITabsMenuItem[] = [];
    private storageKey: string | null = null;

    private options: ITabsMenuOptions = {
        menuClass: 'items-list',
        itemClass: 'items-list__item',
        selectedClass: 'items-list__item--selected',
        iconClass: 'fa-solid',
        notifyClass: 'item__notify',
        notifyActiveClass: 'item__notify--active',
        frameClass: 'item__frame',
    };

    private selectedItem: string;
    private loadedRawsMap: Record<string, string> = {};
    private itemsMap: Record<string, ITabsMenuItem> = {};

    constructor(target: HTMLElement, config: ITabsMenuConfig) {
        super();
        this.target = target;

        this.items = config.items;
        this.validateDuplicateItems();

        this.itemsMap = this.items.reduce((acc, { id, ...rest }) => ({ ...acc, [id]: rest }), {});

        if (config.defaultItem) this.validateItemId(config.defaultItem);
        this.selectedItem = sessionStorage.getItem(config.storageKey as string) || config.defaultItem || this.items[0].id;

        if (config.storageKey) this.registerStorageKey(config.storageKey);
        if (config.options) this.options = config.options;

        this.loadedRawsMap = this.items.reduce((acc, { id }) => ({ ...acc, [id]: null }), {});
        
        // Final setup
        this.createElement();
        this.redirect(this.selectedItem);
    }

    private registerStorageKey(storageKey: string): void {
        this.storageKey = storageKey;
        sessionStorage.setItem(this.storageKey, this.selectedItem);
    }

    /* -------- Validators -------- */

    private validateDuplicateItems(): void {
        const hasDuplicateItems = this.items
            .map(({ id }) => id)
            .some((id, i, arr) => arr.indexOf(id) !== i);

        if (hasDuplicateItems) throw new Error('there cannot be items with the same id');
    }

    private validateItemId(itemId: string): void {
        if (!itemId) throw new Error(`item id "${itemId}" is not defined`);
        if (!this.itemsMap[itemId]) throw new Error(`item id "${itemId}" does not exists`);
    }
    
    /* -------- Methods -------- */
    // Getters:
    public getTarget(): HTMLElement { return this.target; }
    public getSelectedItem(): string { return this.selectedItem; }
    public getItems(): ITabsMenuItem[] { return this.items; }

    // Redirect tab selected to an item.
    public async redirect(itemId: string): Promise<void> {
        this.validateItemId(itemId);
        this.selectItem(itemId);

        await this.loadItemHTML(itemId);
        await this.callItemAction(itemId);
    }

    private selectItem(itemId: string): void {
        this.validateItemId(itemId);
        this.selectedItem = itemId;

        if (this.storageKey) sessionStorage.setItem(this.storageKey, this.selectedItem);

        const { menuClass, itemClass, selectedClass } = this.options;
        const tabs = this.element?.querySelectorAll(`ul.${menuClass} .${itemClass}`) as NodeListOf<HTMLLIElement>;

        tabs.forEach(tab => {
            const tabIsSelected: boolean = this.getTabId(tab) === this.selectedItem;

            tab.classList.toggle(selectedClass as string, tabIsSelected);
            tab.setAttribute('aria-selected', tabIsSelected.toString());
        });
    }

    private getTabId(tab: HTMLLIElement): string {
        // The last class is always the item id or "selected" class.

        const classes = tab.className.split(' ');
        const lastClass = classes.at(-1);

        if (lastClass === this.options.selectedClass) {
            this.validateItemId(classes.at(-2) as string);
            return classes.at(-2) as string;
        }
        
        this.validateItemId(lastClass as string);
        return lastClass as string;
    }

    // Gera o HTML (caso exista) no target.
    private async loadItemHTML(itemId: string): Promise<void> {
        this.validateItemId(itemId);

        if (!this.itemsMap[itemId]?.path) return;

        const preLoadedRaw = this.loadedRawsMap[itemId];
        if (preLoadedRaw) {
            this.target.innerHTML = preLoadedRaw;
            return;
        }
        
        try {
            const res = await fetch(this.itemsMap[itemId].path);
            const rawToRender = await res.text();
            this.loadedRawsMap[itemId] = rawToRender;
            this.target.innerHTML = rawToRender;

            return;
        } catch (e) { throw e; }
    }

    // Executa o script (caso exista).
    private async callItemAction(itemId: string): Promise<void> {
        this.validateItemId(itemId);

        if (!this.itemsMap[itemId]?.action) return;

        try { await this.itemsMap[itemId].action?.(); }
        catch (e: any) { throw new Error(`failed to executing action for ${itemId}:`, e); }
    }
    
    protected createElement(): void {
        const { menuClass } = this.options;

        const menuBar = document.createElement('nav');
        menuBar.classList.add('tabs-menu');

        const itemIds = Object.keys(this.itemsMap); // Reescrever
        const tabsRaw = itemIds.map(id => this.getItemRaw(id)).join('');

        menuBar.innerHTML = `<ul role="tablist" class="${menuClass}">${tabsRaw}</ul>`;
        this.attachRedirectInTabs(menuBar);

        this.element = menuBar;
    }

    private getItemRaw(itemId: string): string {
        this.validateItemId(itemId);

        const { itemClass, iconClass, notifyClass, notifyActiveClass, frameClass } = this.options;

        const { label, faIcon, notify } = this.itemsMap[itemId];

        const notifyElementClasses = [notifyClass, (notify ? notifyActiveClass : '')];
        const notifyElement = notify ? `<span class="${notifyElementClasses.join(' ')}"></span>` : ''

        const faIconFrame = faIcon ? `
            <div class="${frameClass ?? 'frame'}">
                <i class="${iconClass ?? 'fa-solid'} ${faIcon}"></i>
            </div>
        ` : '';

        return `
            <li role="tab" class="${itemClass} ${itemId}" tabindex="0" aria-selected="false">
                ${notifyElement}
                ${faIconFrame}
                <span>${label}</span>
            </li>
        `;
    }

    private attachRedirectInTabs(menuElement: HTMLElement): void {
        const { menuClass, itemClass } = this.options;
        const tabs = menuElement.querySelectorAll(`ul.${menuClass} .${itemClass}`) as NodeListOf<HTMLLIElement>;

        tabs.forEach(tab => {
            tab.addEventListener('click', async () => {
                const itemId = this.getTabId(tab);
                await this.redirect(itemId);
            });
        });
    }
}

export default TabsMenu;
