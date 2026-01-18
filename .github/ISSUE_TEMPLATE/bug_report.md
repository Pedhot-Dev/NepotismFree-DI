name: Bug Report
description: Report a bug in the container implementation.
labels: [bug]
body:
  - type: checkboxes
    attributes:
      label: Prerequisites
      options:
        - label: I have read the project documentation (docs/ARCHITECTURE.md, docs/CONCEPTS.md).
          required: true
        - label: I understand that this is a STRICT DI container and does not support magic/fallbacks.
          required: true
        - label: This issue does not request a feature that violates core DI principles.
          required: true
  - type: textarea
    attributes:
      label: Reproduction Steps
      description: Provide a minimal PHP script reproducing the issue.
      placeholder: |
        $builder = new ContainerBuilder();
        // ...
    validations:
      required: true
  - type: textarea
    attributes:
      label: Expected Behavior
      description: What should happen according to strict DI principles?
    validations:
      required: true
  - type: textarea
    attributes:
      label: Actual Behavior
      description: Describe what actually happened.
    validations:
      required: true
