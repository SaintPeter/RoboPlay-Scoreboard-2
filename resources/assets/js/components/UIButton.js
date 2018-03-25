import React, {Component} from "react";


export default class UIButton extends  Component {
    componentDidMount() {
        this.$node = $(this.refs.button);
        this.$node.button();
        this.$node.parent('div').click(this.props.onClick);
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.remove();
    }

    render() {
        return (
                <div ref="button" data-role="button">
                    {this.props.children}
                </div>
        )
    }
}