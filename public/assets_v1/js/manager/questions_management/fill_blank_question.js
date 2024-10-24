//Handle Blank Field Question

$(document).ready(function () {
    //Initialize .blank-content
    $('.blank-content').each(function (index, element) {
        blankDetection(element,true)
    })
    $(document).on('keyup', '.blank-content', function (){
        blankDetection(this)
    })
    $(document).on('click', '.delete_blank_field', function (){
        deleteBlankField(this)
    })

})

function blankDetection(element,initialize=false) {
    // Get the content from the element
    let content = $(element).html();
    const currentCaretIndex = saveCaretPosition(element);
    let questionContainer = $(element).parents().eq(2)
    let questionId = questionContainer.find('input.question-id').val()

    // Create a unique ID generator
    let uniqueIdGenerator = () => Math.random().toString(36).substr(2, 9);

    // Regular expression to find [blank] markers that are not inside a span
    let blankRegex = /(?<!<span[^>]*class="[^"]*blank-marker[^"]*"[^>]*>)(\[blank\])|(\[blan)|(\[bla)|(\[bl)|(\[b)|(\[)|( k\])|( nk\])|( ank\])|( lank\])/g;

    // Replace matches with spans or partial spans only if they are not already wrapped in a span
    let highlightedContent = content.replace(blankRegex, function (match) {
        if (match === '[blank]') {

            if (!initialize){
                //Add Blank Field
                questionContainer.find('.blank-fields').last().append(blankInput(questionId))
            }

            // Add a unique span for each complete [blank] match
            return `<span id="${uniqueIdGenerator()}" class="blank-marker" style="background-color: #93dfff;" contenteditable="false">[blank]</span>`;
        } else {
            return match; // Leave other partial matches as is
        }
    });

    // Set the updated content in the element
    $(element).html(highlightedContent);

    ///set content
    questionContainer.find(`input[name="question_data[${questionId}][content]"]`).val(highlightedContent)

    //Refresh Counts
    let counters = refresh(questionContainer)

    // just execute in when initialized [true] to create inputs if count of inputs not equal blanks count
    if (initialize){
        if (counters.blanks !== counters.fields){
            let length = parseInt(counters.blanks)-parseInt(counters.fields)
            for (let i=0 ; i < length;i++){
                questionContainer.find('.blank-fields').last().append(blankInput(questionId))
            }
            refresh(questionContainer)
        }

    }

    // Restore the caret position
    setCaretToIndex(element, currentCaretIndex);
}

function setCaretToEnd(element) {
    // Create a range object and set it to the end of the element
    var range = document.createRange();
    var selection = window.getSelection();

    // Ensure the element has content to place the cursor correctly
    if (element.childNodes.length > 0) {
        range.selectNodeContents(element);
        range.collapse(false);  // Collapse the range to the end
        selection.removeAllRanges();  // Clear existing ranges
        selection.addRange(range);    // Add the new range
    }
}

function saveCaretPosition(element) {
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);
        const preCaretRange = range.cloneRange();
        preCaretRange.selectNodeContents(element);
        preCaretRange.setEnd(range.endContainer, range.endOffset);
        return preCaretRange.toString().length; // Returns the caret index
    }
    return 0; // Default to 0 if no selection
}

function setCaretToIndex(element, index) {
    const range = document.createRange();
    const selection = window.getSelection();
    let charIndex = 0, found = false;

    for (let node of element.childNodes) {
        if (node.nodeType === Node.TEXT_NODE) {
            const textLength = node.length;
            if (charIndex + textLength >= index) {
                range.setStart(node, index - charIndex);
                range.collapse(true);
                found = true;
                break;
            }
            charIndex += textLength;
        } else if (node.nodeType === Node.ELEMENT_NODE) {
            const elementLength = node.innerText.length; // Adjust based on your logic
            if (charIndex + elementLength >= index) {
                range.setStartAfter(node);
                range.collapse(true);
                found = true;
                break;
            }
            charIndex += elementLength;
        }
    }

    if (found) {
        selection.removeAllRanges();
        selection.addRange(range);
    }
}

function blankInput(id) {
    let uniqueIdGenerator = () => Math.random().toString(36).substr(2, 9);
    return `<div class="col-4 form-group blank-field mb-2">
                    <div class="d-flex align-items-center mb-1">
                        <label class="counter">0:</label>

                        <button type="button" class="btn btn-sm btn-icon ms-auto btn-danger delete_blank_field"
                                style="height: 16px;width: 16px" data-status="new"><i
                                class="fa fa-close"></i></button>
                    </div>
                    <input required class="form-control"
                           name="question_data[${id}][new][${uniqueIdGenerator()}][content]"
                           type="text">
                </div>`
}

function refresh(questionContainer) {
    let fieldsLength = questionContainer.find('.blank-fields .blank-field').length
    let blanksContentLength = questionContainer.find('.blank-content span.blank-marker').length

    questionContainer.find('.blanks-count').val(blanksContentLength)
    questionContainer.find('.fields-count').val(fieldsLength)

    questionContainer.find('.blank-fields .blank-field').each(function (index, field) {
        $(field).find('.counter').text((index+1)+' : ')
    })
    //console.log({'blanks':blanksContentLength,'fields':fieldsLength})
    return {'blanks':blanksContentLength,'fields':fieldsLength}
}

function deleteBlankField(ele) {
    let questionContainer = $(ele).parents().eq(3)
    let counts = refresh(questionContainer)
    if (counts.fields > counts.blanks){
        if ($(ele).attr('data-status') ==='new'){
            $(ele).parents().eq(1).remove()
            refresh(questionContainer)

        }else {
            showAlert(T_blankFieldTitle,T_blankFieldContent,'warning',true,true,function (callback) {
                if (callback){
                    $(ele).parents().eq(1).remove()
                    refresh(questionContainer)

                }
            })
        }

    }else {
        toastr.error(T_blankFieldErrorMassage)
    }
}
