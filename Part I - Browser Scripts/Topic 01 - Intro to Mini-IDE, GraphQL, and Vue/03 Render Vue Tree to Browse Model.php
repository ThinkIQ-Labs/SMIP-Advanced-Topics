 <?php

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.core.js', array('version' => 'auto', 'relative' => false));
//HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.tiqGraphQL.js', array('version' => 'auto', 'relative' => false));

?>

<!-- item template -->
<script type="text/x-template" id="item-template">
    <li>
        <div
          :class="{bold: isFolder}"
          @click="toggle"
          @dblclick="makeFolder">
          <!-- <span v-if="isFolder">[{{ isOpen ? '-' : '+' }}]</span> -->
          <i v-if="isFolder" v-bind:class="isOpen ? 'fa fa-minus-square' : 'fa fa-plus-square'"
                style="font-size: 1em; color: #0069d9;"></i>
          <span @click="$emit('on-attribute-click', item.id)" v-bind:class="{'text-primary':item.id==activeattrid}">{{item.title}}</span>
        </div>
        <ul v-show="isOpen" v-if="isFolder">
            <tree-item
                class="item"
                v-for="(child, index) in item.children"
                :key="index"
                :item="child"
                @make-folder="$emit('make-folder', $event)"
                @add-item="$emit('add-item', $event)"
                @on-attribute-click="onAttributeClick"
                :activeattrid="activeattrid"
            ></tree-item>
            <!-- <li class="add" @click="$emit('add-item', item)">+</li> -->
        </ul>
    </li>
</script>

<style>
    .item {
        cursor: pointer;
    }

    .bold {
        font-weight: bold;
    }

    ul {
        padding-left: 1em;
        line-height: 1.5em;
        list-style-type: none;
    }

    .tiq_ts_th {
        background-color: #c0d4d8;
    }

    .tiq_ts_td {
        background-color: #fbedd0;
    }
</style>

<div id="app">

    <h1 class="pb-4 text-center">{{title}}</h1>

    <!-- the demo root element -->
    <div class="row">

        <div class="col-5">
            <ul >
                <tree-item class="item" :item="treeData" @make-folder="makeFolder" @add-item="addItem"
                    @on-attribute-click="onAttributeClick" :activeattrid="activeAttrId"></tree-item>
            </ul>
        </div>

    </div>
</div>

<script>

var treeItemComponent = {
    template: "#item-template",
    props: {
        item: Object,
        activeattrid: String
    },
    data: function() {
        return {
            isOpen: false
        };
    },
    computed: {
        isFolder: function() {
            return this.item.children && this.item.children.length;
        }
    },
    methods: {
        toggle: function() {
            if (this.isFolder) {
                this.isOpen = !this.isOpen;
            }
        },
        makeFolder: function() {
            if (!this.isFolder) {
                this.$emit("make-folder", this.item);
                this.isOpen = true;
            }
        },
        onAttributeClick: function(a) {
            this.$emit("on-attribute-click", a);

        }
    }
};

//create instance of the vuejs
var app = createApp({
    data() {
        return {
            title: "Tree View Demo",
            treeData: {},
            things: [],
            activeAttrId: null,

        };
    },
    methods: {

        makeFolder: function(item) {
            Vue.set(item, "children", []);
            this.addItem(item);
        },
        addItem: function(item) {
            item.children.push({
                name: "new_stuff",
                title: "New Stuff"
            });
        },
        onAttributeClick: async function(a) {
            console.log("left", a);
            if (a != 0) {
                this.activeAttrId = a;
            }

        },

    },

    mounted: async function () {
        await this.LoadTreeDataAsync();
    },

    methods: {
        LoadTreeDataAsync: async function(){
            let query = `
                query q2 {
                    things(filter: { systemType: { in: ["organization", "place", "area", "equipment"] } }) {
                        id
                        displayName
                        relativeName
                        fqnList
                        systemType
                    }
                }
            `;
            let queryResponse = await tiqJSHelper.invokeGraphQLAsync(query);


            let things = queryResponse.data.things.sort((a, b) => (a.fqnList.join('/') > b.fqnList.join('/')) ? 1 : -1);

            let treeData = {};

            for (let attributeIndex = 0; attributeIndex < things.length; attributeIndex++) {
                const element = things[attributeIndex];

                if (Object.keys(treeData).length===0) {
                    treeData = {
                        name: element.fqnList[0],
                        title: element.displayName,
                        children: []
                    }
                }

                let currentNode = treeData;
                for (let fqnIndex = 1; fqnIndex < element.fqnList.length; fqnIndex++) {
                    const leaf = element.fqnList[fqnIndex];
                    let aChildLeaf = currentNode.children.find(x => x.name == leaf);
                    if (!aChildLeaf) {
                        aChildLeaf = {
                            name: leaf,
                            title: element.displayName,
                            children: [],
                            id: fqnIndex == element.fqnList.length - 1 ? element.id : 0
                        }
                        currentNode.children.push(aChildLeaf);
                        currentNode.children=currentNode.children.sort((a, b) => (a.title > b.title) ? 1 : -1);
                    }
                    currentNode = aChildLeaf;
                }

            }

            this.treeData = treeData;

        }

    },
})
// define the tree-item component
.component("tree-item", treeItemComponent)
// mount
.mount('#app');
</script>
