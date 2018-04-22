import React, {Component} from "react";

export default class Slider extends Component {
    componentDidMount() {
        this.$node = $(this.refs.slider);
        this.$node.slider();
        this.$node.on('change', this.sendScore);
        this.sendScore()
    }

    componentWillReceiveProps(nextProps) {
        if(this.props.min_entry !== nextProps.min_entry ) {
            this.$node.attr('min', nextProps.min_entry).slider("refresh");
        }
        if(this.props.max_entry !== nextProps.max_entry ) {
            this.$node.attr('max', nextProps.max_entry).slider("refresh");
        }
        if(this.props.type !== nextProps.type) {
            this.$node.attr('step', (nextProps.type ==='score_slider') ? nextProps.multiplier : 1).slider("refresh");
            this.$node.attr('defaultValue', (nextProps.type === 'high_slider') ? nextProps.max_entry : 0).slider("refresh");
        }
        if(this.$node.val() < nextProps.min_entry) {
            this.$node.val(nextProps.min_entry);
        }
        if(this.$node.val() > nextProps.max_entry) {
            this.$node.val(nextProps.max_entry);
        }
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.remove();
    }

    sendScore = () => {
        this.props.onChange(this.$node.val());
    };

    render() {
        let propSet = {
            onChange: this.onChange,
            step: (this.props.type==='score_slider') ? this.props.multiplier : 1,
            min: this.props.min_entry,
            max: this.props.max_entry,
            defaultValue: (this.props.type === 'high_slider') ? this.props.max_entry : Math.max(this.props.min_entry, this.props.base_value),
        };

        return (
                <input ref={"slider"}
                       {...propSet}
                       type="number"
                       data-type="range"
                       className="ui-clear-both ui-shadow-inset ui-body-inherit ui-corner-all ui-slider-input"
                />
        )
    }
}