app.service('bubbleService',[function(){
    var types = {
        name: 'Bubble',
        children: [
            { name: 'Semester', children: [] },
            { name: 'Course', children: [] },
            { name: 'L2PAssignment', children: [] },
            { name: 'L2PMaterialFolder', children: [] },
            { name: 'Attachment', children: [
                { name: 'L2PMaterialAttachment', children: [] },
                { name: 'LinkAttachment', children: [] },
                { name: 'TextAttachment', children: [] },
                { name: 'FileAttachment', children: [
                    { name: 'MediaAttachment', children: [
                        { name: 'ImageAttachment', children: [] },
                        { name: 'VideoAttachment', children: [] },
                    ]},
                ]}

            ]}
        ]
    };
    
    this.getTypeOf = function(bubble, typesParent){
        if(!bubble.bubbleType){
            return false;
        }
        
        var isTopLevel = false;
        if(!typesParent){
            typesParent = types;
            isTopLevel = true;
        }
        
        var suffix = '\\' + typesParent.name;
        
        if(bubble.bubbleType.substr(-suffix.length) === suffix){
            return typesParent.name;
        }
        
        
        for(var i in typesParent.children){
            var childType = this.getTypeOf(bubble, typesParent.children[i]);
            if(childType !== false){
                return childType;
            }
        }
        
        if(isTopLevel){
            return 'Bubble'
        }
        
        return false;
    };
    
    this.typeIsChildOf = function(type, parent){
        // run two simple dfs through types
        var p = null;
        var q = [types];
        
        while(q.length > 0){
            var current = q.pop();
            if(p === null && current.name === parent){
                // dfs for parent
                p = current;
                q = [current];
                // reset stack, when parent is found
                continue;
            } else if(p !== null && current.name === type){
                // return true child was found after parent
                return true;
            }
            for(var i in current.children){
                // add children to stack
                q.push(current.children[i]);
            }
        }
        return false;
    }
    
    this.isOfType = function(bubble, type){
        var bubbleType = this.getTypeOf(bubble);
        if(!bubbleType){
            return false;
        }
        return this.typeIsChildOf(bubbleType, type);
    }
    
    this.isBubble = function(bubble){
        return this.isOfType(bubble, 'Bubble');
    }

    this.isSemester = function(bubble){
        return this.isOfType(bubble, 'Semester');
    }

    this.isCourse = function(bubble){
        return this.isOfType(bubble, 'Course');
    }

    this.isFileAttachment = function(bubble){
        return this.isOfType(bubble, 'FileAttachment');
    }

    this.isAttachment = function(bubble){
        return this.isOfType(bubble, 'Attachment');
    }

    this.isImageAttachment = function(bubble){
        return this.isOfType(bubble, 'ImageAttachment');
    }

    this.isL2PAssignment = function(bubble){
        return this.isOfType(bubble, 'L2PAssignment');
    }

    this.isL2PMaterialAttachment = function(bubble){
        return this.isOfType(bubble, 'L2PMaterialAttachment');
    }

    this.isL2PMaterialFolder = function(bubble){
        return this.isOfType(bubble, 'L2PMaterialFolder');
    }

    this.isLinkAttachment = function(bubble){
        return this.isOfType(bubble, 'LinkAttachment');
    }

    this.isMediaAttachment = function(bubble){
        return this.isOfType(bubble, 'MediaAttachment');
    }

    this.isTextAttachment= function(bubble){
        return this.isOfType(bubble, 'TextAttachment');
    }

    this.isVideoAttachment = function(bubble){
        return this.isOfType(bubble, 'VideoAttachment');
    }
}]);