import React, {Component} from "react";


export default class YesNo extends  Component {
    componentDidMount() {
        this.$node = $(this.refs.flipswitch);
        this.$node.flipswitch();
        this.$node.parent('div').find('a').attr('href', 'javascript:void(0)');
        this.$node.on('change', this.sendScore);
        this.sendScore();
    }

    sendScore = () => {
        this.props.onChange(this.$node.val());
    };

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.remove();
    }

    selectOrder = (type) => {
        if(type === 'noyes') {
            return [
                <option key="0" value="0">No</option>,
                <option key="1" value="1">Yes</option>
            ];
        } else {
            return [
                <option key="1" value="1">Yes</option>,
                <option key="0" value="0">No</option>
            ];
        }
    };

    render() {
        return (
            <h4 dangerouslySetInnerHTML={{ __html: this.props.display_text }} />,
            <select ref="flipswitch"
                    data-role="flipswitch"
                    className="ui-flipswitch-input"
                    tabIndex="-1"
            >
                { this.selectOrder(this.props.type) }
            </select>
        )
    }
}