const express = require('express');
var bodyParser = require('body-parser');
var cors = require('cors');
const neo4j = require('neo4j-driver');

const port = 3000;

const app = express();
app.use(express.json());
app.use(express.urlencoded({
  extended: true
}));

app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE');
    res.header('Access-Control-Allow-Headers', '*');
    next();
});

const driver = neo4j.driver("bolt://localhost:7687", neo4j.auth.basic('neo4j', 'admin'))

app.get('/listAllByType',async function(req,res){
    const session = driver.session()
    var type = req.query.type;
    try {
        let result = await session.writeTransaction(
            tx => tx.run("Match (n:"+type+") return n")
        )
        res.send(result.records)
      }catch(err){
          console.log(err);
      }finally{
          await session.close();
      }
})

app.post('/addNode', async function(req,res){
    const session = driver.session()
    var nodeVal = req.body.NodeVal;
    var nodeValName = req.body.NodeValName;
    var nodeName = req.body.nodeName;
    var name = req.body.name;
    var query = "";

    console.log(nodeVal);
    console.log(nodeValName);   

    if(nodeVal!=null){
        console.log(query);
        if(!Array.isArray(nodeVal)){
            query=query+","+nodeValName+":'"+nodeVal+"'";
        }else{
            for(var i=0;i<nodeVal.length;i++){
                query=query+","+nodeValName[i]+":'"+nodeVal[i]+"'";
            }
        }
    }     

    try {
        console.log(query)
        let result = await session.writeTransaction(
            tx => tx.run("CREATE (a:"+nodeName+"{name:$name"+query+"}) RETURN a",{name:name})
        )
        res.send(result.records)
      }catch(err){
          console.log(err);
      }finally{
          await session.close();
      }
})

app.post('/addRelation',async function(req,res){
    const session = driver.session()

    var Rel1 = req.body.Rel1;
    var Name1 = req.body.rel1Name;
    
    var Rel2 = req.body.Rel2;
    var Name2 = req.body.rel2Name;

    var  RelType = req.body.typeRelation;

    try {
        let result = await session.writeTransaction(
            tx => tx.run("MATCH(a:"+Rel1+"{name:$name1}),(b:"+Rel2+"{name:$name2}) CREATE (a)-[r:"+RelType+"]->(b) RETURN type(r)",{name1:Name1,name2:Name2})
        )
        res.send("poszlo")
      }catch(err){
          console.log(err);
      }finally{
          await session.close();
      }
})

app.get('/searchRelation',async function(req,res){
    const session = driver.session()

    var type = req.query.type;
    var name = req.query.name;

    try {
        let result = await session.writeTransaction(
            tx => tx.run("Match(n:"+type+"{name:$name})-[r]->(d) return r,d",{name:name})
        )
        res.send(result.records)
      }catch(err){
          console.log(err);
      }finally{
          await session.close();
      }
})

app.post('/deleteItem',async function(req,res){
    const session = driver.session()
    var type = req.body.type;
    var name = req.body.name;

    try {
        let result = await session.writeTransaction(
            tx => tx.run("Match (n:"+type+"{name:$name}) DETACH DELETE n",{name:name})
        )
        res.send("poszlo")
      }catch(err){
          console.log(err);
      }finally{
          await session.close();
      }
})

app.listen(port)