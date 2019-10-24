import React from 'react';

export default class ButtonListItem extends React.Component {
    constructor(props) {
        super(props);
    }

    click(e) {
        if (this.props.navigate) {
            e.preventDefault();
            document.dispatchEvent(new Event('wordproof.modal.navigate.' + this.props.navigate));
        }
    }

    render() {
        return (
            <a className={'text-darkblue inline-flex items-center w-full justify-center'} href={this.props.href}
               target="_blank" rel="noopener noreferrer" onClick={ (e) => this.click(e) }>
                <span className={'mr-4'} style={this.iconStyle}>{ this.props.icon }</span> { this.props.children }
            </a>
        );
    }
}
