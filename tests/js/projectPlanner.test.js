import { test } from 'node:test'
import assert from 'node:assert/strict'
import { createProjectBrief, mergeProjectBrief, projectGoals, MESSAGE_LIMIT } from '../../resources/js/lib/projectPlanner.js'

test('every goal creates a localized brief and stable service value', () => {
    for (const goal of projectGoals) {
        const input = { goal: goal.id, stage: 'new', budget: 'discuss' }
        const lt = createProjectBrief(input)
        const en = createProjectBrief(input, 'en')
        assert.equal(lt.service, goal.service)
        assert.equal(en.service, goal.service)
        assert.match(lt.message, /Mano projekto planas/)
        assert.match(en.message, /My project brief/)
        assert.ok(en.message.includes(goal.en))
        assert.ok(lt.message.includes(goal.lt))
        assert.ok(en.message.length < MESSAGE_LIMIT)
    }
})

test('invalid selection cannot inject a service or arbitrary message', () => {
    assert.equal(createProjectBrief({ goal: '<script>', stage: 'new', budget: 'discuss' }), null)
    assert.equal(createProjectBrief({ goal: 'sales', stage: 'unknown', budget: 'discuss' }), null)
    assert.equal(createProjectBrief({ goal: 'sales', stage: 'new', budget: 'unknown' }), null)
})

test('new draft preserves existing text and repeated selection is not duplicated', () => {
    assert.equal(mergeProjectBrief('My own notes', '', 'Draft one'), 'My own notes\n\nDraft one')
    assert.equal(mergeProjectBrief('My own notes\n\nDraft one', 'Draft one', 'Draft two'), 'My own notes\n\nDraft two')
    assert.equal(mergeProjectBrief('Draft one', '', 'Draft one'), 'Draft one')
    assert.equal(mergeProjectBrief('My edited draft', 'Draft one', 'Draft two'), 'My edited draft\n\nDraft two')
})

test('oversized drafts are rejected without losing existing visitor input', () => {
    assert.equal(mergeProjectBrief('x'.repeat(MESSAGE_LIMIT), '', 'New draft'), null)
    assert.equal(mergeProjectBrief('', '', ''), null)
    assert.equal(mergeProjectBrief('', '', 'x'.repeat(MESSAGE_LIMIT)).length, MESSAGE_LIMIT)
})
