import React, {Component} from "react";
import { sprintf } from "sprintf-js";
import rn from 'random-number';

export default class RandomListPopup extends Component {
    constructor(props) {
        super(props);

        this.state = {
            chosenIndexs: props.random_lists.map(this.chooseRandom)
        };

        this.randomLists =  props.random_lists.map(this.formatRandomList);
        this.randomListPopups = props.random_lists.map(this.formatRandomListPopup);
    }

    chooseRandom = (randomList) => {
        return rn({min: 0, max: randomList.elements.length - 1, integer: true});
    };

    formatRandomList = (randomList,index) => {
        let formatted = this.formatElements(randomList.elements[this.state.chosenIndexs[index]], randomList);

        let html = Object.keys(formatted).reduce(function(acc, format){
            let re = new RegExp(format,"g");
            return acc.replace(re,formatted[format]);
        }, randomList.format);

        return <span key={randomList.id} dangerouslySetInnerHTML={{__html: html}} />;
    };

    formatRandomListPopup = (randomList,index) => {
        let formatted = this.formatElements(randomList.elements[this.state.chosenIndexs[index]], randomList);

        let html = Object.keys(formatted).reduce(function(acc, format){
            let re = new RegExp(format,"g");
            return acc.replace(re,formatted[format]);
        }, randomList.popup_format);

        return <span key={randomList.id} className="bigtext" dangerouslySetInnerHTML={{__html: html}} />;
    };

    formatElements = (elements,randomList) => {
        let names = [ 'd1', 'd2', 'd3', 'd4', 'd5' ];
        let formats = [ 'd1_format', 'd2_format', 'd3_format', 'd4_format', 'd5_format' ];
        let output = [];

        for(let i = 0; i < 5; i++) {
            if(formats[i]) {
                output['{' + names[i] + '}'] = sprintf(randomList[formats[i]], elements[names[i]]);
            } else {
                break;
            }
        }
        return output;
    };

    componentDidMount() {
        this.$node = $(this.refs.popup);
        this.$node.popup({
            overlayTheme: "b"
            /*,
            afteropen: () => {
                setTimeout(() => {
                    $.mobile.silentScroll(this.$node.parent().position().top);
                },1);
            }*/
        });
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
                    <h4>Random List</h4>
                    { this.randomLists }
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
                        { this.randomListPopups }
                    </div>
                </div>
            </div>
        )
    }
}
