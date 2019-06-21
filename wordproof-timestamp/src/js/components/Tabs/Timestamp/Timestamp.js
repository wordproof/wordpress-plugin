import React, {Component} from 'react'
import initWallet from '../../../wallet';
import getBonus from '../../../bonus';

export default class Timestamp extends Component {
  constructor(props) {
    super(props);
    this.state = {
      wallet: null,
      walletAvailable: '',
      isLoading: true,
      boxClasses: 'box',
      bonusStatus: null,
      bonusIsLoading: null,
      bonusMessage: null,
      bonusBoxClasses: 'box'
    }
  }

  componentDidMount() {
    this.getWallet();
  }

  getWallet = async () => {
    if (this.state.wallet === null) {
      const wallet = initWallet();
      try {
        await wallet.connect();
        if (!wallet.authenticated) { await wallet.login(); }

        this.registerWalletConnection();
        this.setState({
          wallet: wallet,
          walletAvailable: true,
          isLoading: false,
          boxClasses: 'box box-success',
          bonusIsLoading: true
        });
        this.checkBonus(wallet.accountInfo.account_name, wordproofData.network);
      } catch (error) {
        this.setState({
          walletAvailable: false,
          isLoading: false,
          boxClasses: 'box box-failed'
        });
      }
    }
    return this.state.wallet;
  }

  registerWalletConnection = () => {
    return fetch(wordproofData.ajaxURL, {
      method: "POST",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
      },
      body:
      'action=wordproof_wallet_connection' +
      '&security='+ wordproofData.ajaxSecurity,
    }).then((response) => {
      return response.json();
    })
    .catch(error => console.error(error));
  }

   checkBonus = async(accountName, chain) => {
    const bonus = await getBonus(accountName, chain);
    switch(bonus.status) {
      case 'success':
        this.setState({
          bonusStatus: 'success',
          bonusBoxClasses: 'box box-success',
          bonusMessage: bonus.message,
          bonusIsLoading: false
        });
        break;
      case 'failed':
        this.setState({
          bonusStatus: 'failed',
          bonusBoxClasses: 'box box-success',
          bonusMessage: bonus.message,
          bonusIsLoading: false
        });
        break;
      default: //no_change
        this.setState({
          bonusStatus: 'no_change',
          bonusBoxClasses: 'box',
          bonusMessage: bonus.message,
          bonusIsLoading: false,
        });
    }
    console.log('bonus', bonus);
  }

  render() {
    return (
      <div>
        <div className="vo-card">
          <h3>You are ready to timestamp your content!</h3>
          <p>Each time you timestamp content, you need the Scatter Wallet to sign your transaction. Thus, make sure that the Scatter application is opened and unlocked before you load the page.</p>
          <p>To verify whether the set-up has been successful, you can use this tab. The box below shows whether WordProof Timestamp has successfully connected to your Scatter Wallet.</p>

          <div className={this.state.boxClasses}>
            { this.state.isLoading ?
              <div><div className="wordproof-connecting"><img className="loading-spinner" height="64px" width="64px" src="/wp-admin/images/spinner-2x.gif" alt="loading" />Connecting...</div></div> : ''
            }
            { this.state.walletAvailable === true ?
              <p><span className="dashicons dashicons-yes-alt"></span> All set! There is a connection with your Scatter wallet.</p> : ''
            }

            { this.state.walletAvailable === false ?
              <p>WordProof Timestamp can&apos;t connect to the Scatter wallet. Open & unlock the wallet and refresh this page to try again.</p> : ''
            }
          </div>

          { (this.state.bonusIsLoading !== null) ?
          <div className={this.state.bonusBoxClasses}>
            { this.state.bonusIsLoading ?
              <div><div className="wordproof-connecting"><img className="loading-spinner" height="64px" width="64px" src="/wp-admin/images/spinner-2x.gif" alt="loading" />Connecting...</div></div> : ''
            }
            { this.state.bonusStatus !== null ?
              <p>
                { (this.state.bonusStatus !== 'failed') ? <span className="dashicons dashicons-yes-alt"></span> : <span className="dashicons dashicons-no-alt"></span>}
                { this.state.bonusMessage }
                { (this.state.bonusStatus !== 'failed') ? " You are ready to time-stamp. Make sure to keep your Scatter wallet open and unlocked!" : "" }</p> : ''
            }
          </div> : ''}

          <h3>Help! WordProof Timestamp does not connect to my Scatter Wallet!</h3>
          <p>Blockchain is not an easy subject and uses accounts, wallets and transactions. This is what makes the technology so safe, but also what makes it challenging to create easy-to-use blockchain applications. If the set-up did not work properly, here are a few steps you can take:</p>
          <ol>
            <li>Make sure the Scatter Wallet application is open on your computer and that you unlocked it (entered your passphrase).</li>
            <li>Run the Setup Wizard again and follow each step very carefully. Make sure to save your keys!</li>
          </ol>
        </div>
      </div>
    )
  }
}
