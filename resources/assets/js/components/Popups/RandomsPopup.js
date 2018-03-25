import React, {Component} from "react";
import { sprintf } from "sprintf-js";
import rn from 'random-number';

export default class RandomsPopup extends Component {
    constructor(props) {
        super(props);

        this.randoms = props.randoms.map(this.formatRandom);
    }

    formatRandom = function(random) {
        let rand1, rand2;

        rand1 = rn({ min: random.min1, max: random.max1, integer: true});
        rand2 = rn({ min: random.min2, max: random.max2, integer: true});

        if(random.may_not_match) {
            while(rand1 == rand2) {
                rand2 = rn({ min: random.min2, max: random.max2, integer: true});
            }
        }

        switch (random.type) {
            case 'single':
                return <p key={random.id} dangerouslySetInnerHTML={{ __html: sprintf(random.format, rand1) }} />;
            case 'dual':
                return <p key={random.id} dangerouslySetInnerHTML={{ __html: sprintf(random.format, rand1, rand2) }} />;
        }
    };

    componentDidMount() {
        this.$node = $(this.refs.popup);
        this.$node.popup();
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.remove();
    }

    onPopoutClick = () =>
    {
        this.$node.popup("open");
    };

    render() {
        return (
            <div>
                <div className="ui-body ui-body-a">
                    <button
                        onClick={this.onPopoutClick}
                       id="random_popout"
                       data-rel="popup"
                       data-position-to="window"
                       className="ui-btn ui-btn-inline pull-right">Popout</button>
                    <h4>Randoms</h4>
                    { this.randoms }
                </div>
                <div ref={"popup"}
                    data-role="popup"
                     data-history='false'
                     id="randomPopup"
                     className="ui-corner-all">
                    <div role="banner" data-role="header" data-theme="a" className="ui-corner-top ui-header ui-bar-a">
                        <h1 role="heading" className="ui-title">Random Number</h1>
                    </div>
                    <div role="main" className="ui-corner-bottom ui-content center">
                        <span className="bigtext">{ this.randoms }</span>
                    </div>
                </div>
            </div>
        )
    }
}
