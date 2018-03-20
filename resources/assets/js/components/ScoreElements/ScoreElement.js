import React, { Component } from "react";

export default class ScoreElement extends Component {
    constructor(props) {
        super(props);
        this.state = {
            id: props.id,
            base: props.base_value,
            multi: (props.type==='score_slider') ? 1 : props.multiplier,
            element_number: props.element_number,
            score_map: props.score_map,
            score: 0,
            scoreChange: props.scoreChange

        }
    }

    // Calculate the individual score for this element when it changes
    handleScoreChange = (value) => {
      const baseScore = this.state.base + (value * this.state.multi);
      let newScore = baseScore;
      if(this.state.score_map.length) {
          for(let i = this.state.score_map.length - 1; i > -1; i--) {
              if(baseScore >= this.state.score_map[i].i) {
                  newScore = this.state.score_map[i].v * 1;
                  break;
              }
          }
      }
      // Update my state
      this.setState({score: newScore});

      // Send the change up to ScoreChallenge
      let updatedScore = {};
      updatedScore[this.state.element_number] = {id: this.state.id, score: newScore};
      this.state.scoreChange(updatedScore);
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
