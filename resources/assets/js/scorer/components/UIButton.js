import React, {Component} from "react";
import ReactDOMServer from 'react-dom/server';


export default class UIButton extends  Component {
    componentDidMount() {
        this.$node = $(this.refs.button);
        this.$node.button();
        this.$node.parent('div').click(this.props.onClick);
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillReceiveProps(nextProps) {
        if(this.props.children !== nextProps.children) {
            this.$node.html(ReactDOMServer.renderToStaticMarkup(nextProps.children));
        }
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