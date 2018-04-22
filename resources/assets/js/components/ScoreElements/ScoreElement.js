import React, { Component } from "react";

export default class ScoreElement extends Component {
    constructor(props) {
        super(props);

        this.multi = (props.type==='score_slider') ? 1 : props.multiplier;
        this.state = {
            score: Math.max(this.props.min_entry, this.props.base_value),
        }
    }

    // Calculate the individual score for this element when it changes
    handleScoreChange = (value) => {
        const baseScore = this.props.base_value + (value * this.multi) + (Math.pow(value,2) * this.props.multiplier2);
        let newScore = baseScore;
        if(this.props.score_map.length) {
          for(let i = this.props.score_map.length - 1; i > -1; i--) {
              if(baseScore >= this.props.score_map[i].i) {
                  newScore = this.props.score_map[i].v * 1;
                  break;
              }
          }
        }

        // Enforce min and max values
        if(this.props.enforce_limits) {
            newScore = Math.min(this.props.max_entry, Math.max(this.props.min_entry, newScore));
        }

        // Update my state
        this.setState({score: newScore});

        // Send the change up to ScoreChallenge
        this.props.scoreChange({[this.props.element_number]: newScore });
    };

    render() {
        return (
            <li className="ui-field-contain ui-li-static ui-body-inherit">
                <span className="ui-li-count score_display">{ this.state.score }</span>
                <h4 dangerouslySetInnerHTML={{ __html: this.props.display_text }} />
                {React.cloneElement(this.props.children, { onChange: this.handleScoreChange })}
            </li>
        );
    }

}
