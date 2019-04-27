import React, {Component} from "react";


export default class CollapsibleBlock extends  Component {
  componentDidMount() {
    this.$node = $(this.refs.cblock);
    this.$node.collapsible({
      collapsed: !this.props.show,
      expand: this.props.expanded,
      collapse: this.props.collapsed
    });
    //this.$node.parent('div').click(this.props.onClick);
  }

  shouldComponentUpdate() {
    return false;
  }

  // componentWillReceiveProps(nextProps) {
  //   if(this.props.children !== nextProps.children) {
  //     this.$node.html(nextProps.children);
  //   }
  // }

  componentWillUnmount() {
    //this.$node.remove();
  }

  render() {
    return (
      <div ref="cblock" data-role="collapsible">
        <h3>{this.props.header}</h3>
        <p style={{ padding: '1em'}} dangerouslySetInnerHTML={{__html: this.props.content}} />
      </div>
    )
  }
}