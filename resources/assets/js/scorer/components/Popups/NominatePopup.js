import React, {Component} from "react";
import UIButton from "../UIButton";
import YesNo from "../ScoreElements/YesNo"

export default class NominatePopup extends Component {
  constructor(props) {
    super(props);

    this.state = {
      spirit: props.noms.spirit,
      teamwork: props.noms.teamwork,
      persevere: props.noms.persevere,
    }
  }


    componentDidMount() {
        this.$node = $(this.refs.nominatePopup);
        this.$node.popup({
            afterclose: () => {
                this.props.onCancel();
            },
            afteropen: () => {
                setTimeout(() => {
                    $.mobile.silentScroll(this.$node.parent().position().top);
                },100);
            }
        });
    }

    // shouldComponentUpdate() {
    //     return false;
    // }

    componentWillUnmount() {
        this.$node.popup("destroy");
    }

    componentWillReceiveProps(nextProps) {
        if(!this.props.visible && nextProps.visible) {
            this.$node.popup("open");
        }

        if(this.props.visible && !nextProps.visible) {
            this.$node.popup("close");
        }
    }

    updateState = (node, value) => {
      this.setState({[node]: value});
    };

    saveNominations = () => {
      this.props.saveNoms(this.state);
    };

    render() {
        return (
            <div ref="nominatePopup" data-role="popup" data-history='false' id="submitPopup" className="ui-corner-all">
                <div role="banner" data-role="header" data-theme="a" className="ui-corner-top ui-header ui-bar-a">
                    <h1 aria-level="1" role="heading" className="ui-title">Nominate</h1>
                    <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete"
                       data-iconpos="notext" className="ui-btn-right ui-link ui-btn ui-icon-back ui-btn-icon-notext ui-shadow ui-corner-all"></a>
                </div>
                <div role="main" className="ui-corner-bottom ui-content center popupContent">
                  <div className="ui-body ui-body-a ui-corner-all text-left">
                    These nominations will be discussed at the end of the competition period.
                    You may wish to make notes about team performance.
                  </div>
                  <h4>Spirit Award</h4>
                  <YesNo
                    type="noyes"
                    onChange={(val) => this.updateState('spirit',val)}
                    display_text="Spirit Award"
                    defaultValue={this.props.noms.spirit}
                  />
                  <h4>Teamwork Award</h4>
                  <YesNo
                    type="noyes"
                    onChange={(val) => this.updateState('teamwork',val)}
                    display_text="Teamwork Award"
                    defaultValue={this.props.noms.teamwork}
                  />
                  <h4>Perseverance Award</h4>
                  <YesNo
                    type="noyes"
                    onChange={(val) => this.updateState('persevere',val)}
                    display_text="Perseverance Award"
                    defaultValue={this.props.noms.persevere}
                  />
                  <fieldset className="ui-grid-a">
                    <div className="ui-block-a">
                      <UIButton onClick={this.props.onCancel}>
                        Cancel
                      </UIButton>
                    </div>
                    <div className="ui-block-b">
                      <UIButton onClick={this.saveNominations}>
                        Save
                      </UIButton>
                    </div>
                  </fieldset>
                </div>
            </div>               
        )
    }
}

NominatePopup.defaultProps = {
    visible: false,
};
