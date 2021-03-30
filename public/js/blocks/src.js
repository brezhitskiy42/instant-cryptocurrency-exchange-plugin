import Select from 'react-select';

const { registerBlockType, getCategories, setCategories } = wp.blocks;
const { Fragment } = wp.element;
const { PanelBody } = wp.components;
const { InspectorControls, PanelColorSettings } = wp.editor;

(function() {
  const categories = [
    ...getCategories(),
    { slug: 'cryptocurrency', title: cryptoExchange.cryptocurrencyExchange }
  ];

  setCategories(categories);
})();

registerBlockType('instant-cryptocurrency-exchange/crypto-exchange', {
  title: cryptoExchange.cryptocurrencyExchangeForm,
  category: 'cryptocurrency',
  attributes: {
    fromCoin: { type: 'object' },
    toCoin: { type: 'object' },
		fromCoinIcon: { type: 'string' },
		toCoinIcon: { type: 'string' },
    foreground: { type: 'string' },
    background: { type: 'string' }
  },
  edit({ attributes, setAttributes }) {
    function setFromCoin(coin) {
			const fromCoinIcon = getCoinIcon(coin);
      setAttributes({ fromCoin: coin, fromCoinIcon });
    }

    function setToCoin(coin) {
			const toCoinIcon = getCoinIcon(coin);
      setAttributes({ toCoin: coin, toCoinIcon });
    }

    function setForegroundColor(color) {
      setAttributes({ foreground: color });
    }

    function setBackgroundColor(color) {
      setAttributes({ background: color });
    }

		function getCoinIcon(coinFromSelect) {
			if (!coinFromSelect) return '';

			let coinIcon = '';
			const coinsList = JSON.parse(cryptoExchange.coinsList);
			for (let coin of coinsList) {
				if (coinFromSelect.value === coin.symbol) {
					coinIcon = coin.image;
					break;
				}
			}

			return coinIcon;
		}

    const coins = JSON.parse(cryptoExchange.coinsList).map(coin => {
      return { value: coin.symbol, label: coin.name };
    });

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title={cryptoExchange.coinsSettings}>
						<div className="components-base-control">
		          <div className="components-base-control__field">
		            <Select
									className="default-coin-select__ce"
		              isClearable={true}
		              placeholder={cryptoExchange.fromCoin}
									onChange={setFromCoin}
									defaultValue={attributes.fromCoin}
		              value={attributes.fromCoin}
		              options={coins} />
		          </div>
		        </div>
		        <div className="components-base-control">
		          <div className="components-base-control__field">
		            <Select
									className="default-coin-select__ce"
		              isClearable={true}
		              placeholder={cryptoExchange.toCoin}
									onChange={setToCoin}
									defaultValue={attributes.toCoin}
		              value={attributes.toCoin}
		              options={coins} />
		          </div>
		        </div>
					</PanelBody>
					<PanelColorSettings
						title={cryptoExchange.colorSettings}
						colorSettings={[
							{ onChange: setForegroundColor, value: attributes.foreground, label: cryptoExchange.foregroundColor },
							{ onChange: setBackgroundColor, value: attributes.background, label: cryptoExchange.backgroundColor }
						]}
					/>
				</InspectorControls>
				<div className="crypto-exchange-blocks__ce">
					<div className="coins-icons__ce">
						{attributes.fromCoinIcon &&
						<div className="coin-icon__ce"><img src={attributes.fromCoinIcon} alt=""/></div>
						}
            {attributes.fromCoinIcon && attributes.toCoinIcon &&
            <div className="coin-icon-exchange__ce"><i class="fa fa-exchange"></i></div>
            }
						{attributes.toCoinIcon &&
						<div className="coin-icon__ce"><img src={attributes.toCoinIcon} alt=""/></div>
						}
					</div>
					<p className="subtext__ce">{cryptoExchange.exchangeSubtext}</p>
				</div>
			</Fragment>
		);
  },
  save({ attributes }) {
    const from = attributes.fromCoin ? ` from="${attributes.fromCoin.value}"` : '';
    const to = attributes.toCoin ? ` to="${attributes.toCoin.value}"` : '';
    const foreground = attributes.foreground ? ` foreground="${attributes.foreground}"` : '';
    const background = attributes.background ? ` background="${attributes.background}"` : '';

    let shortcode = '[instant_crypto_exchange';
    shortcode += from + to + foreground + background;
    shortcode += ']';

    return shortcode;
  }
});

registerBlockType('instant-cryptocurrency-exchange/crypto-exchange-transactions', {
  title: cryptoExchange.cryptoExchangeTransactions,
  category: 'cryptocurrency',
  attributes: {
    maxNumber: { default: 10, type: 'string' },
    type: { default: 'global', type: 'string' },
    foreground: { type: 'string' },
    background: { type: 'string' }
  },
  edit({ attributes, setAttributes }) {

    function setMaxNumber(e) {
      setAttributes({ maxNumber: e.target.value });
    }

    function setType(e) {
      setAttributes({ type: e.target.value });
    }

    function setForegroundColor(color) {
      setAttributes({ foreground: color });
    }

    function setBackgroundColor(color) {
      setAttributes({ background: color });
    }

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={cryptoExchange.transactionsSettings}>
            <div className="components-base-control">
              <div className="components-base-control__field">
                <label className="components-base-control__label" for="transactions-max-number">{cryptoExchange.maxNumber}</label>
                <input
                  defaultValue={attributes.maxNumber}
                  className="components-text-control__input"
                  type="number"
                  min="1"
                  step="1"
                  id="transactions-max-number"
                  placeholder="10"
                  onChange={setMaxNumber} />
              </div>
            </div>
            <div className="components-base-control">
              <div className="components-base-control__field">
                <label className="components-base-control__label" for="transactions-type">{cryptoExchange.transactions}</label>
                <select
                  value={attributes.type}
                  id="transactions-type"
                  className="components-select-control__input"
                  onChange={setType}>
                  <option value="global">{cryptoExchange.all}</option>
                  <option value="local">{cryptoExchange.thisWebsiteOnly}</option>
                </select>
              </div>
            </div>
          </PanelBody>
          <PanelColorSettings
						title={cryptoExchange.colorSettings}
						colorSettings={[
							{ onChange: setForegroundColor, value: attributes.foreground, label: cryptoExchange.foregroundColor },
							{ onChange: setBackgroundColor, value: attributes.background, label: cryptoExchange.backgroundColor }
						]}
					/>
        </InspectorControls>
        <div className="crypto-exchange-blocks__ce">
					<p className="subtext__ce">{cryptoExchange.transactionsSubtext}</p>
				</div>
      </Fragment>
    );
  },
  save({ attributes }) {
    const count = attributes.maxNumber ? ` count="${attributes.maxNumber}"` : '';
    const type = attributes.type ? ` type="${attributes.type}"` : '';
    const foreground = attributes.foreground ? ` foreground="${attributes.foreground}"` : '';
    const background = attributes.background ? ` background="${attributes.background}"` : '';

    let shortcode = '[instant_crypto_exchange_transactions';
    shortcode += count + type + foreground + background;
    shortcode += ']';

    return shortcode;
  }
});
