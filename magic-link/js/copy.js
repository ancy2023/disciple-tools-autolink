import { css, html, LitElement } from 'lit';

export class DtCopy extends LitElement {
  static get styles() {
    return css`
      :root {
        font-size: inherit;
      }
      svg {
        width: 1em;
        height: auto;
      }
      svg path {
        fill: currentcolor;
      }
    `;
  }
  render() {
    return html`
      <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M19.3724 2.8334L17.0562 0.604098C16.6543 0.217303 16.1093 2.86033e-06 15.541 0L7.85714 0C6.67366 0 5.71429 0.923398 5.71429 2.0625V4.125H2.14286C0.959375 4.125 0 5.0484 0 6.1875V19.9375C0 21.0766 0.959375 22 2.14286 22H12.1429C13.3263 22 14.2857 21.0766 14.2857 19.9375V17.875H17.8571C19.0406 17.875 20 16.9516 20 15.8125V4.2918C20 3.7448 19.7742 3.22019 19.3724 2.8334ZM11.875 19.9375H2.41071C2.33967 19.9375 2.27154 19.9103 2.22131 19.862C2.17108 19.8136 2.14286 19.7481 2.14286 19.6797V6.44531C2.14286 6.37694 2.17108 6.31136 2.22131 6.26301C2.27154 6.21466 2.33967 6.1875 2.41071 6.1875H5.71429V15.8125C5.71429 16.9516 6.67366 17.875 7.85714 17.875H12.1429V19.6797C12.1429 19.7481 12.1146 19.8136 12.0644 19.862C12.0142 19.9103 11.946 19.9375 11.875 19.9375ZM17.5893 15.8125H8.125C8.05396 15.8125 7.98583 15.7853 7.9356 15.737C7.88536 15.6886 7.85714 15.6231 7.85714 15.5547V2.32031C7.85714 2.25194 7.88536 2.18636 7.9356 2.13801C7.98583 2.08966 8.05396 2.0625 8.125 2.0625H12.8571V5.84375C12.8571 6.4133 13.3368 6.875 13.9286 6.875H17.8571V15.5547C17.8571 15.6231 17.8289 15.6886 17.7787 15.737C17.7285 15.7853 17.6603 15.8125 17.5893 15.8125ZM17.8571 4.8125H15V2.0625H15.43C15.501 2.0625 15.5692 2.08966 15.6194 2.138L17.7787 4.21631C17.8036 4.24025 17.8233 4.26868 17.8368 4.29996C17.8502 4.33124 17.8571 4.36477 17.8571 4.39862V4.8125Z" fill="#575757"/>
      </svg>`
  }
}

window.customElements.define('dt-copy', DtCopy);