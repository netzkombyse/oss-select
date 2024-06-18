// import
import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import PageLoadingIndicatorUtil from 'src/utility/loading-indicator/page-loading-indicator.util';
import PseudoModalUtil from 'src/utility/modal-extension/pseudo-modal.util';
import DomAccess from 'src/helper/dom-access.helper';


// plugin
export default class NetzkomOssSelector extends Plugin {

    // plugin configuration
    static options = {
        getStatusUrl: '',
        getModalPopupUrl: '',
        setContextUrl: '',
        class: 'netzkom-oss-selector-modal',
        openSelectorParent: 'body',
        openSelector:'.netzkom-oss-selector-open'
    };

    // on init
    init() {

        // get this
        const me = this;

        // set urls
        this.options.getStatusUrl = window.router['widgets.netzkom.oss-selector.get-status'];
        this.options.getModalPopupUrl = window.router['widgets.netzkom.oss-selector.get-modal'];
        this.options.setContextUrl = window.router['widgets.netzkom.oss-selector.set-context'];

        // create http client
        this._httpClient = new HttpClient(window.accessKey, window.contextToken);

        // stop current http client
        this._httpClient.abort();

        const userLang = navigator.language || navigator.userLanguage;

        document.querySelector(this.options.openSelectorParent).addEventListener('click', function(e) {

            if(!e.target.matches(me.options.openSelector)){
                return;
            }
            //close off-canvas
            document.querySelector('.offcanvas-close').click();
            me.loadModal();
        });
    }

    // ...
    loadModal() {
        // get this
        const me = this;

        // set loading
        PageLoadingIndicatorUtil.create();

        // stop current http client
        me._httpClient.abort();

        // get the modal
        me._httpClient.get(
            me.options.getModalPopupUrl,
            function(content) { me.openModal(content); }
        );
    }

    // ...
    openModal(content) {
        // get this
        const me = this;

        // create a modal
        const pseudoModal = new PseudoModalUtil(content);

        // remove the loading indicator
        PageLoadingIndicatorUtil.remove();

        // open the modal
        pseudoModal.open(
            me.onOpenModal.bind(me, pseudoModal)
        );

        // add our custom class
        pseudoModal.getModal().classList.add(
            me.options.class
        );
    }

    // ...
    onOpenModal(pseudoModal) {
        // get this
        const me = this;
        const modal = document.querySelector('div.modal.' + me.options.class);

        // click listener to submit
        modal.querySelector('.netzkom-oss-selector button').addEventListener('click', function() {
            // set the params
            const params = {
                _csrf_token: modal.querySelector('input[name="_csrf_token"]').value,
                countryId: modal.querySelector('select.country').value,

            };
            // close the modal
            pseudoModal.close();

            // set loading
            PageLoadingIndicatorUtil.create();

            // send request
            me._httpClient.post(me.options.setContextUrl, JSON.stringify(params), () => location.reload());
        });
    }
}
