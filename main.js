var log = console.log.bind(console);
var map = {};
var file = $.load(argv[1]).split("\t").map(fn(a) {
  return a.split("\t");
});
for(var i in file) {
  map[file[i][0]] = file[i][1];
}
var dict = $.load(argv[2]).filter(fn(a) {
  return a.slice(0, 3) !== ";;;";
});
dict.map(fn(a) {
  return a
    .replace("  ", " ")
    .trim()
    .explode(" ", 2);
    .replace("/(\w\w)(\d)/", "\1 \2")
    .implode(" ")
    .explode("  ");
}).then(fn(a) {
  var o = a[0];
  a = a.shift().map(fn(b) {
    return [a[0], map[b], map[b].remove("[\ˈ\ˌ]")]; //this is a wrapper for a.replace("xyz", "")
  });
  for(var b in a) {
    log(a[b].pad(25));
  }
});
