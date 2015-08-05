var WhathoodClickResultNeighborhood = React.createClass({
  render: function() {
    return (
      <div>Neighborhood {this.props.data.name}</div>
    );
  }
});

var WhathoodClickResult = React.createClass({
  render: function() {
    var rows = new Array();
    for (var i = 0; i < this.props.neighborhoods.length; i++) {
      console.log(this.props);
      rows.push(<WhathoodClickResultNeighborhood data={this.props.neighborhoods[i]} />);
    }
    return (
      <div className="whathoodClickResult">
        {this.props.region.name}
        {rows}
      </div>
    );
  }
});
