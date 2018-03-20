import React, {Component} from "react";

export default class Slider extends Component {
    componentDidMount() {
        this.$node = $(this.refs.slider);
        this.$node.slider();
        this.$node.on('change', this.sendScore);
        this.sendScore()
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
            defaultValue: (this.props.type === 'high_slider') ? this.props.max_entry : 0,
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